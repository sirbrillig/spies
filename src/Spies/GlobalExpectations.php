<?php
namespace Spies;

class GlobalExpectations {
	public static $global_expectations = [];

	public static function add_expectation( $expectation ) {
		self::$global_expectations[] = $expectation;
	}

	public static function resolve_delayed_expectations() {
		array_map( function( $expectation ) {
			if ( ! $expectation->was_verified ) {
				try {
					$expectation->verify();
				} catch ( \Exception $e ) {
				}
			}
		}, self::$global_expectations );
	}

	public static function clear_all_expectations() {
		self::$global_expectations = [];
	}
}
