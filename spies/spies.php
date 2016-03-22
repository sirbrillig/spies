<?php
namespace Spies;

class PassedArgument {
	public $index = 0;
	public function __construct( $index ) {
		$this->index = $index;
	}
}


/**
 * Ways to create a global function spy:
 *
 * $spy = get_spy_for( 'add_one ');
 * expect_spy( $spy )->to_be_called->with( 5 );
 * add_one( 5 );
 *
 * Or:
 *
 * $spy = get_spy_for( 'add_one ');
 * add_one( 5 );
 * expect_spy( $spy )->to_have_been_called->with( 5 );
 *
 * Or:
 *
 * $spy = new \Spies\Spy();
 * $spy->call( 5 );
 * expect_spy( $spy )->to_have_been_called->with( 5 );
 *
 * Ways to create a global function stub (which is also a spy):
 *
 * mock_function( 'add_one' )->with( 5 )->and_return( 6 );
 * mock_function( 'add_one' )->with( 6 )->and_return( 7 );
 *
 * Or:
 *
 * $spy = mock_function( 'add_one' );
 * $spy->when_called->with( 5 )->will_return( 6 );
 * $spy->when_called->with( 6 )->will_return( 7 );
 */
class Spy {
	public $function_name = null;

	public $when_called = null;

	private $call_record = [];

	// A record of all the currently defined global spies, which we need to find
	// when the spied functions are triggered. Keyed by function name.
	public static $global_spies = [];

	// A record of all the functions we have globally defined.
	public static $global_functions = [];

	// The Stub argument expectations
	private $with_arguments = null;

	// The Stub function return values
	private $return_value = null;
	private $conditional_returns = [];

	public function __construct( $function_name = null ) {
		$this->when_called = $this;
		if ( $function_name ) {
			$this->function_name = $function_name;
			$this->create_global_function( $function_name );
			self::$global_spies[ $function_name ] = $this;
		}
	}

	/**
 	 * Allows Spy to be called as a function
	 *
	 * Alias for `call`
	 */
	public function __invoke() {
		$args = func_get_args();
		return $this->call_with_array( $args );
	}

	/**
 	 * Return the function name
	 *
	 * @return string The function name
	 */
	public function get_function_name() {
		if ( isset( $this->function_name ) ) {
			return $this->function_name;
		}
		return 'anonymous function';
	}

	public static function get_spy_for( $function_name ) {
		$spy = self::get_global_spy( $function_name );
		if ( isset( $spy ) ) {
			return $spy;
		}
		return new Spy( $function_name );
	}

	public static function stub_function( $function_name ) {
		return get_spy_for( $function_name );
	}

	public static function passed_arg( $index ) {
		return new PassedArgument( $index );
	}

	/**
	 * Return the spy for a global function
	 *
	 * You should not need to call this directly.
	 *
	 * @param string $function_name The name of the function
	 * @return Spy|null The spy or null if it was not found
	 */
	private static function get_global_spy( $function_name ) {
		if ( ! isset( self::$global_spies[ $function_name ] ) ) {
			return null;
		}
		return self::$global_spies[ $function_name ];
	}

	/**
 	 * Clear all globally defined spies
	 *
	 * You should not need to call this directly. See `\Spies\Expectation::finish_spying()`
	 *
	 * The global functions themselves cannot be removed, but after calling this,
	 * any calls to the global functions will throw an Exception unless they are
	 * spied on again.
	 */
	public static function clear_all_spies() {
		self::$global_spies = [];
	}

	/**
	 * Call this mocked function.
	 *
	 * You can define the behavior of the mocked function by using `with`,
	 * `will_return`, etc. See the documentation on Stubs and Mocks. Otherwise
	 * this will return null.
	 *
	 * @param mixed $arg,... Any arguments for the function
	 * @return mixed Whatever the mocked function returns
	 */
	public function call() {
		$args = func_get_args();
		return $this->call_with_array( $args );
	}

	/**
 	 * Call this mocked function with an array of arguments.
	 *
	 * Same as `call`, but with an array of arguments instead of any number.
	 *
	 * @param array $args Any arguments for the function in an array
	 * @return mixed Whatever the mocked function returns
	 */
	public function call_with_array( $args ) {
		$this->record_function_call( $args );
		return $this->get_return_for( $args );
	}

	/**
 	 * Clear the record of calls for this spy
	 */
	public function clear_call_record() {
		$this->call_record = [];
	}

	/**
	 * Get the raw record of calls for this spy
	 *
	 * Each call record is an array of the form:
	 *
	 * [ 'args' => array, ... ]
	 *
	 * Where the value of the 'args' key is an array of arguments passed to this
	 * spy when it was called.
	 *
	 * @return array An array of call records
	 */
	public function get_called_functions() {
		return $this->call_record;
	}

	/**
	 * Instruct this spy to return a particular value
	 *
	 * Alias for `and_return`, designed to be used like this:
	 *
	 * $add_one = mock_function( 'add_one' );
	 * $add_one->when_called->with( 5 )->will_return( 6 );
	 *
	 * @param mixed $value The value to return when this spy is called
	 * @return Spy This Spy
	 */
	public function will_return( $value ) {
		return $this->and_return( $value );
	}

	/**
	 * Instruct this spy to return the first argument when called
	 *
	 * Alias for `and_return( \Spies\Spy::passed_arg( 1 ) )`
	 *
	 * @return Spy This Spy
	 */
	public function and_return_first_argument() {
		return $this->and_return( Spy::passed_arg( 1 ) );
	}

	/**
	 * Instruct this spy to return a particular value
	 *
	 * Alias for `and_return`.
	 *
	 * @param mixed $value The value to return when this spy is called
	 * @return Spy This Spy
	 */
	public function that_returns( $value ) {
		return $this->and_return( $value );
	}

	/**
	 * Instruct this spy to return a particular value
	 *
	 * Used like this:
	 *
	 * mock_function( 'add_one' )->and_return( 6 );
	 *
	 * If the function `with` has been used on this Spy, this function will assign
	 * the return value only when the arguments specified in `with` are used.
	 *
	 * mock_function( 'add_one' )->with( 4 )->and_return( 5 );
 	 *
	 * @param mixed $value The value to return when this spy is called
	 * @return Spy This Spy
	 */
	public function and_return( $value ) {
		if ( isset( $this->with_arguments ) ) {
			$this->conditional_returns[] = [ 'args' => $this->with_arguments, 'return' => $value ];
			$this->with_arguments = null;
		}
		$this->return_value = $value;
		return $this;
	}

	/**
	 * Instruct this spy to only mock calls with certain arguments
	 *
	 * This changes the behavior of the `and_return` function to only return a
	 * particular value if certain arguments are present when the function is
	 * called.
	 *
 	 * @param mixed $arg... The arguments to use when defining a behavior
	 * @return Spy This spy
	 */
	public function with() {
		$args = func_get_args();
		$this->with_arguments = $args;
		return $this;
	}

	/**
 	 * Return true if the spy was called
	 *
	 * @return boolean True if the spy was called
	 */
	public function was_called() {
		return ( count( $this->get_called_functions() ) > 0 );
	}

	/**
 	 * Return true if the spy was called a certain number of times
	 *
	 * @param integer $times The number of times the function should have been called
	 * @return boolean True if the spy was called $times times
	 */
	public function was_called_times( $times ) {
		return ( count( $this->get_called_functions() ) === $times );
	}

	/**
 	 * Return true if the spy was called with a certain number of arguments
	 *
	 * @param mixed $arg... The arguments to look for in the call record
	 * @return boolean True if the spy was called with the arguments
	 */
	public function was_called_with() {
		$args = func_get_args();
		$matching_calls = array_filter( $this->get_called_functions(), function( $call ) use ( $args ) {
			return ( self::do_args_match( $call[ 'args' ], $args ) );
		} );
		return ( count( $matching_calls ) > 0 );
	}

	/**
	 * Handle a global spy function call
	 *
	 * You should not need to call this directly.
	 */
	public static function handle_call_for( $function_name, $args ) {
		$spy = self::get_global_spy( $function_name );
		if ( ! isset( $spy ) ) {
			throw new \Exception( 'Call to undefined function ' . $function_name );
		}
		return $spy->call_with_array( $args );
	}

	/**
	 * Compare argument lists
	 *
	 * You should not need to call this directly.
	 */
	public static function do_args_match( $a, $b ) {
		if ( $a === $b ) {
			return true;
		}
		$index = 0;
		foreach( $a as $arg ) {
			if ( ! self::do_vals_match( $arg, $b[ $index ] ) ) {
				return false;
			}
			$index ++;
		}
		return true;
	}

	private static function do_vals_match( $a, $b ) {
		if ( $a === $b ) {
			return true;
		}
		if ( $a instanceof \Spies\AnyValue || $b instanceof \Spies\AnyValue ) {
			return true;
		}
		return false;
	}

	/**
 	 * Add a function call to the call record
	 *
	 * You should not need to call this directly.
	 */
	public function record_function_call( $args ) {
		$this->call_record[] = [ 'args' => $args ];
	}

	/**
	 * Determine the return value for this spy
	 *
	 * You should not need to call this directly.
	 */
	public function get_return_for( $args ) {
		if ( $this->conditional_returns ) {
			$conditional_return = array_reduce( $this->conditional_returns, function( $carry, $condition ) use ( $args ) {
				if ( self::do_args_match( $condition['args'], $args ) ) {
					return $condition['return'];
				}
				return $carry;
			} );
			if ( $conditional_return ) {
				if ( $conditional_return instanceof PassedArgument ) {
					return $args[ $conditional_return->index ];
				}
				return $conditional_return;
			}
		}
		if ( isset( $this->return_value ) ) {
			$return = $this->return_value;
			if ( $return instanceof PassedArgument ) {
				return $args[ $return->index ];
			}
			return $return;
		}
		return null;
	}

	/**
	 * Create a function in the global namespace.
	 *
	 * You should not need to call this directly.
	 *
 	 * @SuppressWarnings(PHPMD.EvalExpression)
	 */
	private function create_global_function( $function_name ) {
		// If we already have a spy for this function, just reset its call record.
		if ( isset( self::$global_spies[ $function_name ] ) ) {
			self::$global_spies[ $function_name ]->clear_call_record();
			return;
		}
		// If we don't have a spy, but we have defined this global function before,
		// do nothing.
		if ( isset( self::$global_functions[ $function_name ] ) ) {
			return;
		}
		if ( function_exists( $function_name ) ) {
			throw new \Exception( 'Attempt to mock existing function ' . $function_name );
		}
		$function_eval = <<<EOF
function $function_name() {
	return \Spies\Spy::handle_call_for( '$function_name', func_get_args() );
}
EOF;
		eval( $function_eval );
		// Save the name of this function so we know that we already defined it.
		self::$global_functions[ $function_name ] = true;
	}
}

