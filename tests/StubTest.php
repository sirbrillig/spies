<?php

/**
 * @runTestsInSeparateProcesses
 */
class StubTest extends PHPUnit_Framework_TestCase {
	public function test_mock_function_returns_a_spy() {
		$stub = \Spies\mock_function( 'test_stub' );
		$this->assertTrue( $stub instanceof \Spies\Spy );
	}

	public function test_mock_function_creates_a_global_function() {
		\Spies\mock_function( 'test_stub' );
		$this->assertTrue( function_exists( 'test_stub' ) );
	}

	public function test_stub_and_return_returns_the_value_when_the_stub_is_called() {
		$stub = \Spies\mock_function( 'test_stub' )->and_return( 8 );
		$this->assertEquals( 8, $stub() );
	}

	public function test_stub_will_return_returns_the_value_when_the_stub_is_called() {
		$stub = \Spies\mock_function( 'test_stub' )->will_return( 8 );
		$this->assertEquals( 8, $stub() );
	}

	public function test_stub_when_called_with_sets_a_conditional_return_value_on_the_stub() {
		\Spies\mock_function( 'test_stub' )->when_called->with( 'foo' )->will_return( 5 );
		\Spies\mock_function( 'test_stub' )->when_called->with( 'bar' )->will_return( 6 );
		$this->assertEquals( 5, test_stub( 'foo' ) );
		$this->assertEquals( 6, test_stub( 'bar' ) );
	}

	public function test_stub_with_conditional_returns_will_return_unconditional_value_when_called_with_unexpected_parameters() {
		\Spies\mock_function( 'test_stub' )->when_called->with( 'foo' )->will_return( 5 );
		\Spies\mock_function( 'test_stub' )->will_return( 7 );
		$this->assertEquals( 5, test_stub( 'foo' ) );
		$this->assertEquals( 7, test_stub( 'bar' ) );
	}

	public function test_stub_and_return_passed_arg_will_return_the_passed_argument_with_the_appropriate_index() {
		\Spies\mock_function( 'test_stub' )->and_return( \Spies\Spy::passed_arg( 1 ) );
		$this->assertEquals( 'bar', test_stub( 'foo', 'bar' ) );
	}

	public function test_stub_and_return_first_argument_will_return_the_first_passed_argument() {
		\Spies\mock_function( 'test_stub' )->and_return_first_argument();
		$this->assertEquals( 'foo', test_stub( 'foo', 'bar' ) );
	}

	public function test_stub_and_return_second_argument_will_return_the_second_passed_argument() {
		\Spies\mock_function( 'test_stub' )->and_return_second_argument();
		$this->assertEquals( 'bar', test_stub( 'foo', 'bar' ) );
	}

	public function test_stub_and_return_with_a_function_will_call_that_function_when_the_stub_is_called() {
		\Spies\mock_function( 'test_stub' )->and_return( function( $arg ) {
			return $arg + 1;
		} );
		$this->assertEquals( 6, test_stub( 5 ) );
	}
}
