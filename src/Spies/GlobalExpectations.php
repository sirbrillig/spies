<?php
namespace Spies;

class GlobalExpectations {
	public static $global_expectations = [];

	public static function add_expectation( $expectation ) {
		self::$global_expectations[] = $expectation;
	}

	public static function resolve_delayed_expectations() {
		foreach ( self::$global_expectations as $expectation ) {
			if ( ! $expectation->was_verified ) {
				$expectation->verify();
			}
		}
	}

	public static function clear_all_expectations() {
		self::$global_expectations = [];
	}
}
