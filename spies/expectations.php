<?php
namespace Spies;

class AnyValue {
	public $value = 'ANYTHING';
}

class Expectation {
	private $spy = null;

	public $to_be_called;
	public $to_have_been_called;

	public $negation = null;
	public $expected_args = null;

	public static $delayed_expectations = [];

	public function __construct( $spy ) {
		if ( is_string( $spy ) ) {
			throw new \Exception( 'Expectations require a Spy but you passed a string: ' . $spy );
		}
		$this->spy = $spy;
		$this->to_be_called = $this;
		$this->to_have_been_called = $this;
	}

	public static function any() {
		return new AnyValue();
	}

	public static function finish_spying() {
		self::resolve_delayed_expectations();
		\Spies\Spy::clear_all_spies();
	}

	public static function resolve_delayed_expectations() {
		array_map( function( $func ) {
			call_user_func( $func );
		}, self::$delayed_expectations );
		self::clear_all_expectations();
	}

	public static function clear_all_expectations() {
		self::$delayed_expectations = [];
	}

	public static function delay_expectation( $func ) {
		self::$delayed_expectations[] = $func;
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
		self::delay_expectation( function() use ( $args ) {
			$result = call_user_func_array( [ $this->spy, 'was_called_with' ], $args );
			$description = 'Expected ' . $this->spy->function_name . ' to be called with ' . json_encode( $args ) . ' but instead ';
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
		self::delay_expectation( function() {
			$result = $this->spy->was_called();
			$description = 'Expected ' . $this->spy->function_name . ' to be called but it was not called at all.';
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
		self::delay_expectation( function() use ( $count ) {
			$called_functions = $this->spy->get_called_functions();
			$actual = count( $called_functions );
			$description = 'Expected ' . $this->spy->function_name . ' to be called ' . $count . ' times ';
			if ( isset( $this->expected_args ) ) {
				$actual = count( array_filter( $called_functions, function( $call ) {
					return ( Spy::do_args_match( $call['args'], $this->expected_args ) );
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
