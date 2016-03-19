<?php
namespace Spies;

class Spy {
	public $function_name = null;

	public static $called_functions = [];
	public static $global_functions = [];

	private $with_arguments = null;

	public function __construct( $function_name = null ) {
		$this->function_name = $function_name;
		if ( $function_name ) {
			$this->create_global_function( $function_name );
		}
	}

	private static function get_called_functions_for( $function_name ) {
		if ( ! isset( self::$called_functions[ $function_name ] ) ) {
			throw new \Exception( 'No spies found for the function ' . $function_name );
		}
		return self::$called_functions[ $function_name ];
	}

	public static function clear_all_spies() {
		self::$called_functions = [];
		foreach( array_keys( self::$global_functions ) as $function_name ) {
			self::$global_functions[ $function_name ] = [ 'conditional_return' => [], 'return' => null ];
		}
	}

	public function get_called_functions() {
		return self::get_called_functions_for( $this->function_name );
	}

	public function and_return( $value ) {
		self::get_called_functions_for( $this->function_name );
		if ( isset( $this->with_arguments ) ) {
			self::$global_functions[ $this->function_name ]['conditional_return'][] = [ 'return' => $value, 'args' => $this->with_arguments ];
			return;
		}
		self::$global_functions[ $this->function_name ]['return'] = $value;
	}

	public function with() {
		self::get_called_functions_for( $this->function_name );
		$args = func_get_args();
		$this->with_arguments = $args;
		return $this;
	}

	public function was_called() {
		$called_functions = self::get_called_functions_for( $this->function_name );
		return ( count( $called_functions ) > 0 );
	}

	public function was_called_times( $times ) {
		$called_functions = self::get_called_functions_for( $this->function_name );
		return ( count( $called_functions ) === $times );
	}

	public function was_called_with() {
		$args = func_get_args();
		$called_functions = self::get_called_functions_for( $this->function_name );
		$matching_calls = array_filter( $called_functions, function( $call ) use ( $args ) {
			return ( self::do_args_match( $call[ 'args' ], $args ) );
		} );
		return ( count( $matching_calls ) > 0 );
	}

	public static function handle_call_for( $function_name, $args ) {
		if ( ! isset( self::$called_functions[ $function_name ] ) ) {
			throw new \Exception( 'Call to undefined function ' . $function_name );
		}
		self::record_function_call( $function_name, $args );
		return self::get_return_for( $function_name, $args );
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

	private static function record_function_call( $function_name, $args ) {
		self::$called_functions[ $function_name ][] = [ 'args' => $args ];
	}

	private static function get_return_for( $function_name, $args ) {
		$value = array_reduce( self::$global_functions[ $function_name ]['conditional_return'], function( $carry, $data ) use ( $args ) {
			if ( self::do_args_match( $data['args'], $args ) ) {
				return $data['return'];
			}
			return $carry;
		} );
		if ( isset( $value ) ) {
			return $value;
		}
		if ( isset( self::$global_functions[ $function_name ]['return'] ) ) {
			return self::$global_functions[ $function_name ]['return'];
		}
		return null;
	}

	private function create_global_function( $function_name ) {
		if ( isset( self::$called_functions[ $function_name ] ) ) {
			self::$called_functions[ $function_name ] = [];
			return;
		}
		if ( isset( self::$global_functions[ $function_name ] ) ) {
			self::$called_functions[ $function_name ] = [];
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
		self::$global_functions[ $function_name ] = [ 'conditional_return' => [], 'return' => null ];
		self::$called_functions[ $function_name ] = [];
	}
}

