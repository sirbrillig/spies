<?php
namespace Spies;

class Expectation {
	private $spy = null;

	public $to_be_called;
	public $to_have_been_called;

	public $negation = null;
	public $expected_args = null;

	public $delayed_expectations = [];

	public static $global_expectations = [];

	public function __construct( $spy ) {
		if ( is_string( $spy ) ) {
			throw new \Exception( 'Expectations require a Spy but I was passed a string: ' . $spy );
		}
		if ( ! $spy instanceof Spy ) {
			throw new \Exception( 'Expectations require a Spy' );
		}
		$this->spy = $spy;
		$this->to_be_called = $this;
		$this->to_have_been_called = $this;
		self::$global_expectations[] = $this;
	}

	public static function expect_spy( $spy ) {
		return new Expectation( $spy );
	}

	public static function any() {
		return new AnyValue();
	}

	public static function resolve_delayed_expectations() {
		array_map( function( $expectation ) {
			$expectation->verify();
		}, self::$global_expectations );
	}

	public static function clear_all_expectations() {
		self::$global_expectations = [];
	}

	/**
 	 * Delay an expected behavior
	 *
	 * This will store a function to be run when `verify` is called on this
	 * Expectation. You can delay as many behavior functions as you like. Each
	 * behavior function should throw an Exception if it fails.
	 *
	 * Also adds this Expectation to a global list which allows all Expectations
	 * to be verified using `resolve_delayed_expectations` (and thus `finish_spying`).
	 *
	 * @param function $behavior A function that describes the expected behavior
	 */
	private function delay_expectation( $behavior ) {
		$this->delayed_expectations[] = $behavior;
	}

	/**
 	 * Verify all behaviors in this Expectation
	 */
	public function verify() {
		array_map( function( $func ) {
			call_user_func( $func );
		}, $this->delayed_expectations );
	}

	public function __get( $key ) {
		if ( $key === 'not' ) {
			$this->negation = true;
			return $this;
		}
	}

	private function format_arguments_for_output( $called_functions ) {
		return implode( ", ", array_map( function( $call ) {
			return json_encode( $call['args'] );
		}, $called_functions ) );
	}

	public function with() {
		$args = func_get_args();
		$this->expected_args = $args;
		$this->delay_expectation( function() use ( $args ) {
			$result = call_user_func_array( [ $this->spy, 'was_called_with' ], $args );
			$description = 'Expected "' . $this->spy->get_function_name() . '" to be called with ' . json_encode( $args ) . ' but instead ';
			$called_functions = $this->spy->get_called_functions();
			if ( count( $called_functions ) === 1 ) {
				$description .= 'it was called with ' . $this->format_arguments_for_output( [ $called_functions[0] ] );
			}
			if ( count( $called_functions ) === 0 ) {
				$description .= 'it was not called at all.';
			}
			if ( count( $called_functions ) > 1 ) {
				$description .= 'it was called with each of these sets of arguments ' . $this->format_arguments_for_output( $called_functions );
			}
			if ( $this->negation ) {
				\PHPUnit_Framework_Assert::assertFalse( $result, $description );
				return;
			}
			\PHPUnit_Framework_Assert::assertTrue( $result, $description );
		} );
		return $this;
	}

	public function to_be_called() {
		$this->delay_expectation( function() {
			$result = $this->spy->was_called();
			$description = 'Expected "' . $this->spy->get_function_name() . '" to be called but it was not called at all.';
			if ( $this->negation ) {
				\PHPUnit_Framework_Assert::assertFalse( $result, $description );
				return;
			}
			\PHPUnit_Framework_Assert::assertTrue( $result, $description );
		} );
		return $this;
	}

	public function to_have_been_called() {
		$args = func_get_args();
		return call_user_func_array( [ $this, 'to_be_called' ], $args );
	}

	public function once() {
		return $this->times( 1 );
	}

	public function times( $count ) {
		$this->delay_expectation( function() use ( $count ) {
			$called_functions = $this->spy->get_called_functions();
			$actual = count( $called_functions );
			$description = 'Expected "' . $this->spy->get_function_name() . '" to be called ' . $count . ' times ';
			if ( isset( $this->expected_args ) ) {
				$actual = count( array_filter( $called_functions, function( $call ) {
					return ( Helpers::do_args_match( $call['args'], $this->expected_args ) );
				} ) );
				$description .= 'with the arguments ' . json_encode( $this->expected_args ) . ' ';
			}
			$description .= 'but it was called ' . $actual . ' times';
			if ( $actual > 0 ) {
				$description .= ' with each of these sets of arguments ' . $this->format_arguments_for_output( $called_functions );
			}
			if ( $this->negation ) {
				\PHPUnit_Framework_Assert::assertNotEquals( $count, $actual, $description );
				return;
			}
			\PHPUnit_Framework_Assert::assertEquals( $count, $actual, $description );
		} );
		return $this;
	}
}
