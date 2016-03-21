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

	public function __construct( $function_name ) {
		$this->function_name = $function_name;
		$this->when_called = $this;
		$this->create_global_function( $function_name );
		self::$global_spies[ $function_name ] = $this;
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

	private static function get_global_spy( $function_name ) {
		if ( ! isset( self::$global_spies[ $function_name ] ) ) {
			return null;
		}
		return self::$global_spies[ $function_name ];
	}

	public static function clear_all_spies() {
		self::$global_spies = [];
	}

	public function clear_call_record() {
		$this->call_record = [];
	}

	public function get_called_functions() {
		return $this->call_record;
	}

	public function will_return( $value ) {
		return $this->and_return( $value );
	}

	public function and_return( $value ) {
		if ( isset( $this->with_arguments ) ) {
			$this->conditional_returns[] = [ 'args' => $this->with_arguments, 'return' => $value ];
			$this->with_arguments = null;
		}
		$this->return_value = $value;
		return $this;
	}

	public function with() {
		$args = func_get_args();
		$this->with_arguments = $args;
		return $this;
	}

	public function was_called() {
		return ( count( $this->get_called_functions() ) > 0 );
	}

	public function was_called_times( $times ) {
		return ( count( $this->get_called_functions() ) === $times );
	}

	public function was_called_with() {
		$args = func_get_args();
		$matching_calls = array_filter( $this->get_called_functions(), function( $call ) use ( $args ) {
			return ( self::do_args_match( $call[ 'args' ], $args ) );
		} );
		return ( count( $matching_calls ) > 0 );
	}

	public static function handle_call_for( $function_name, $args ) {
		$spy = self::get_global_spy( $function_name );
		if ( ! isset( $spy ) ) {
			throw new \Exception( 'Call to undefined function ' . $function_name );
		}
		$spy->record_function_call( $args );
		return $spy->get_return_for( $args );
	}

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

	public function record_function_call( $args ) {
		$this->call_record[] = [ 'args' => $args ];
	}

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

