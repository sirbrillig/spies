<?php
namespace Spies;

class Expectation {
	// Syntactic sugar; these just return the Expectation
	public $to_be_called;
	public $to_have_been_called;

	// If true, `verify()` will throw Exceptions instead of using PHPUnit_Framework_Assert
	public $throw_exceptions = false;
	// If true, `verify()` will return an error description instead of using PHPUnit_Framework_Assert
	public $silent_failures = false;

	public static $global_expectations = [];

	private $spy = null;
	private $negation = null;
	private $expected_args = null;
	private $delayed_expectations = [];

	public function __construct( $spy ) {
		if ( is_string( $spy ) ) {
			throw new \InvalidArgumentException( 'Expectations require a Spy but I was passed a string: ' . $spy );
		}
		if ( ! $spy instanceof Spy ) {
			throw new \InvalidArgumentException( 'Expectations require a Spy' );
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
	 * Magic function so that the `not` property can be used to negate this
	 * Expectation.
	 */
	public function __get( $key ) {
		if ( $key === 'not' ) {
			$this->negation = true;
			return $this;
		}
	}

	/**
 	 * Verify all behaviors in this Expectation
	 *
	 * By default it will use PHPUnit_Framework_Assert to create assertions for
	 * each behavior.
	 *
	 * If `throw_exceptions` is set to true, instead it will throw an exception
	 * for each failure.
	 *
	 * If `silent_failures` is set to true, instead it will return the description
	 * of the first failure it finds, or null if all behaviors passed.
	 *
	 * @return string|null The first failure description if there is a failure
	 */
	public function verify() {
		foreach( $this->delayed_expectations as $behavior ) {
			$description = call_user_func( $behavior );
			if ( $description ) {
				return $description;
			}
		}
	}

	/**
	 * Set expected arguments
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @param mixed $arg... The arguments we expect
	 * @return Expectation This Expectation to allow chaining
	 */
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
				return $this->assertFalse( $result, $description );
			}
			return $this->assertTrue( $result, $description );
		} );
		return $this;
	}

	/**
 	 * Set the expectation that the Spy was called
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function to_be_called() {
		$this->delay_expectation( function() {
			$result = $this->spy->was_called();
			$description = 'Expected "' . $this->spy->get_function_name() . '" to be called but it was not called at all.';
			if ( $this->negation ) {
				return $this->assertFalse( $result, $description );
			}
			return $this->assertTrue( $result, $description );
		} );
		return $this;
	}

	/**
 	 * Set the expectation that the Spy was called
	 *
	 * Alias for `to_be_called`
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function to_have_been_called() {
		$args = func_get_args();
		return call_user_func_array( [ $this, 'to_be_called' ], $args );
	}

	/**
	 * Set the expectation that the Spy was called once
	 *
	 * Alias for `times(1)`
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function once() {
		return $this->times( 1 );
	}

	/**
	 * Set the expectation that the Spy was called a number of times
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
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
				return $this->assertNotEquals( $count, $actual, $description );
			}
			return $this->assertEquals( $count, $actual, $description );
		} );
		return $this;
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

	private function format_arguments_for_output( $called_functions ) {
		return implode( ", ", array_map( function( $call ) {
			return json_encode( $call['args'] );
		}, $called_functions ) );
	}

	private function assertTrue( $value, $description ) {
		if ( ! $this->throw_exceptions && ! $this->silent_failures && class_exists( 'PHPUnit_Framework_Assert' ) ) {
			\PHPUnit_Framework_Assert::assertTrue( $value, $description );
			return;
		}
		if ( ! $value ) {
			if ( $this->silent_failures ) {
				return $description;
			}
			throw new UnmetExpectationException( $description );
		}
	}

	private function assertFalse( $value, $description ) {
		if ( ! $this->throw_exceptions && ! $this->silent_failures && class_exists( 'PHPUnit_Framework_Assert' ) ) {
			\PHPUnit_Framework_Assert::assertFalse( $value, $description );
			return;
		}
		if ( $value ) {
			if ( $this->silent_failures ) {
				return $description;
			}
			throw new UnmetExpectationException( $description );
		}
	}

	private function assertEquals( $a, $b, $description ) {
		if ( ! $this->throw_exceptions && ! $this->silent_failures && class_exists( 'PHPUnit_Framework_Assert' ) ) {
			\PHPUnit_Framework_Assert::assertEquals( $a, $b, $description );
			return;
		}
		if ( $a !== $b ) {
			if ( $this->silent_failures ) {
				return $description;
			}
			throw new UnmetExpectationException( $description );
		}
	}

	private function assertNotEquals( $a, $b, $description ) {
		if ( ! $this->throw_exceptions && ! $this->silent_failures && class_exists( 'PHPUnit_Framework_Assert' ) ) {
			\PHPUnit_Framework_Assert::assertNotEquals( $a, $b, $description );
			return;
		}
		if ( $a === $b ) {
			if ( $this->silent_failures ) {
				return $description;
			}
			throw new UnmetExpectationException( $description );
		}
	}
}
