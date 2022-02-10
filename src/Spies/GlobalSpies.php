<?php
namespace Spies;

class GlobalSpies {
	// A record of all the currently defined global spies, which we need to find
	// when the spied functions are triggered. Keyed by function name.
	public static $global_spies = [];

	// A record of all the functions we have globally defined.
	public static $global_functions = [];

	// A record of all the existing functions that have been redefined
	public static $redefined_functions = [];

	// A record of all functions that did not have a definition and we generated one.
	public static $generated_functions = [];

	public static function create_global_spy( $function_name ) {
		$spy = new Spy( $function_name );
		self::create_global_function( $function_name );
		self::$global_spies[ $function_name ] = $spy;
		return $spy;
	}

	/**
	 * Clear all globally defined spies
	 *
	 * You should not need to call this directly. See `\Spies\finish_spying()`
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
	public static function handle_call_for( $function_name, $args, $options = [] ) {
		$spy = self::get_global_spy( $function_name );
		if ( ! isset( $spy ) ) {
			throw new UndefinedFunctionException( 'Call to undefined function ' . $function_name );
		}
		return $spy->call_with_array( $args, $options );
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
		// Replace or create the function
		if ( function_exists( $function_name ) ) {
			self::replace_global_function( $function_name );
		} else {
			$function_eval = self::generate_function_with( $function_name );
			eval( $function_eval );
			self::$generated_functions[ $function_name ] = true;
		}
		// Save the name of this function so we know that we already defined it.
		self::$global_functions[ $function_name ] = true;
	}

	public static function restore_original_global_functions() {
		if ( ! function_exists( 'Patchwork\restore' ) ) {
			return;
		}
		foreach ( array_values( self::$redefined_functions ) as $handle ) {
			\Patchwork\restore( $handle );
		}
		
		// forget the name of the global functions we already defined.
		self::$global_functions = [];
	}

	public static function call_original_global_function( $function_name, $args ) {
		if ( ! isset( self::$redefined_functions[ $function_name ] ) ) {
			return;
		}

		// skip if the function did not exist before (i.e. had no "original" function)
		if ( isset( self::$generated_functions[ $function_name ] ) ) {
			return;
		}

		if ( ! function_exists( 'Patchwork\restore' ) ) {
			return;
		}
		\Patchwork\restore( self::$redefined_functions[ $function_name ] );
		$value = call_user_func_array( $function_name, $args );
		self::replace_global_function( $function_name );
		return $value;
	}

	private static function replace_global_function( $function_name ) {
		if ( ! function_exists( 'Patchwork\redefine' ) || ! function_exists( 'Patchwork\relay' ) ) {
			throw new \Exception( 'Attempt to mock existing function ' . $function_name . '; please load Patchwork first in your test bootstrap file. See here for an example: https://github.com/sirbrillig/spies/blob/master/README.md#spying-and-mocking-existing-functions' );
			return;
		}
		self::$redefined_functions[ $function_name ] = \Patchwork\redefine( $function_name, function() use ( $function_name ) {
			$value = \Spies\GlobalSpies::handle_call_for( $function_name, func_get_args(), [ 'return_falsey_objects' => true ] );
			return self::filter_return_for( $value );
		} );
	}

	private static function filter_return_for( $return ) {
		if ( $return instanceof FalseyValue ) {
			return $return->get_value();
		}
		return $return;
	}

	private static function generate_function_with( $function_name ) {
		$namespace_text = '';
		$function_name_text = $function_name;
		$name_parts = explode( '\\', $function_name );
		if ( count( $name_parts ) > 1 ) {
			if ( empty( $name_parts[0] ) ) {
				array_shift( $name_parts );
			}
			$function_name_text = array_pop( $name_parts );
			$namespace_text = 'namespace ' . implode( '\\', $name_parts ) . ';';
		}
		$function_eval = <<<EOF
$namespace_text

function $function_name_text() {
	return \Spies\GlobalSpies::handle_call_for( '$function_name', func_get_args() );
}
EOF;
		return $function_eval;
	}
}
