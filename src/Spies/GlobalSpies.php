<?php
namespace Spies;

class GlobalSpies {
	// A record of all the currently defined global spies, which we need to find
	// when the spied functions are triggered. Keyed by function name.
	public static $global_spies = [];

	// A record of all the functions we have globally defined.
	public static $global_functions = [];

	public static function create_global_spy( $function_name ) {
		$spy = new Spy( $function_name );
		self::create_global_function( $function_name );
		self::$global_spies[ $function_name ] = $spy;
		return $spy;
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
	 * Return the spy for a global function
	 *
	 * You should not need to call this directly.
	 *
	 * @param string $function_name The name of the function
	 * @return Spy|null The spy or null if it was not found
	 */
	public static function get_global_spy( $function_name ) {
		if ( ! isset( self::$global_spies[ $function_name ] ) ) {
			return null;
		}
		return self::$global_spies[ $function_name ];
	}

	/**
	 * Handle a global spy function call
	 *
	 * You should not need to call this directly.
	 */
	public static function handle_call_for( $function_name, $args ) {
		$spy = self::get_global_spy( $function_name );
		if ( ! isset( $spy ) ) {
			throw new UndefinedFunctionException( 'Call to undefined function ' . $function_name );
		}
		return $spy->call_with_array( $args );
	}

	/**
	 * Create a function in the global namespace.
	 *
	 * You should not need to call this directly.
	 *
 	 * @SuppressWarnings(PHPMD.EvalExpression)
	 */
	private static function create_global_function( $function_name ) {
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
	return \Spies\GlobalSpies::handle_call_for( '$function_name', func_get_args() );
}
EOF;
		eval( $function_eval );
		// Save the name of this function so we know that we already defined it.
		self::$global_functions[ $function_name ] = true;
	}
}
