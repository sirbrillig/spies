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

	public function test_mock_function_with_a_namespace_creates_a_namespaced_function() {
		\Spies\mock_function( '\TestNamespace\test_namespaced_stub' );
		$this->assertTrue( function_exists( '\TestNamespace\test_namespaced_stub' ) );
	}

	public function test_mock_function_with_two_levels_of_a_namespace_creates_a_namespaced_function() {
		\Spies\mock_function( '\TestNamespace\Level2\test_namespaced_stub' );
		$this->assertTrue( function_exists( '\TestNamespace\Level2\test_namespaced_stub' ) );
	}

	public function test_stub_and_return_returns_the_value_when_the_stub_is_called() {
		$stub = \Spies\mock_function( 'test_stub' )->and_return( 8 );
		$this->assertEquals( 8, $stub() );
	}

	public function test_stub_and_return_returns_the_value_when_the_stub_is_called_even_if_the_value_is_empty() {
		$stub = \Spies\mock_function( 'test_stub' )->and_return( '' );
		$this->assertEquals( '', $stub() );
	}

	public function test_stub_and_return_returns_the_value_when_the_stub_is_called_even_if_the_value_is_false() {
		$stub = \Spies\mock_function( 'test_stub' )->and_return( false );
		$this->assertEquals( false, $stub() );
	}

	public function test_stub_with_argument_and_return_returns_the_value_when_the_stub_is_called_even_if_the_return_value_is_empty() {
		$stub = \Spies\mock_function( 'test_stub' )->with( 5 )->and_return( [] );
		$this->assertEquals( [], $stub( 5 ) );
	}

	public function test_stub_and_return_returns_the_value_when_the_stub_is_called_for_a_namespaced_function() {
		\Spies\mock_function( '\TestNamespace\test_namespaced_stub' )->and_return( 8 );
		$this->assertEquals( 8, \TestNamespace\test_namespaced_stub() );
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

	public function test_stub_when_called_with_match_array_sets_a_conditional_return_value_on_the_stub() {
		\Spies\mock_function( 'test_stub' )->will_return( 4 );
		\Spies\mock_function( 'test_stub' )->when_called->with( \Spies\match_array( [ 'type' => 'Bokoblin' ] ) )->will_return( 5 );
		\Spies\mock_function( 'test_stub' )->when_called->with( \Spies\match_array( [ 'type' => 'Moblin' ] ) )->will_return( 6 );
		$this->assertEquals( 5, test_stub( [ 'name' => 'Bobo', 'type' => 'Bokoblin' ] ) );
		$this->assertEquals( 6, test_stub( [ 'name' => 'Grup', 'type' => 'Moblin' ] ) );
		$this->assertEquals( 4, test_stub( [ 'name' => 'Corb', 'type' => 'Lizafos' ] ) );
	}

	public function test_stub_with_conditional_returns_will_return_unconditional_value_when_called_with_unexpected_parameters() {
		\Spies\mock_function( 'test_stub' )->when_called->with( 'foo' )->will_return( 5 );
		\Spies\mock_function( 'test_stub' )->will_return( 7 );
		$this->assertEquals( 5, test_stub( 'foo' ) );
		$this->assertEquals( 7, test_stub( 'bar' ) );
	}

	public function test_stub_with_unconditional_return_first_will_return_unconditional_value_when_called_with_unexpected_parameters() {
		\Spies\mock_function( 'test_stub' )->will_return( 7 );
		\Spies\mock_function( 'test_stub' )->when_called->with( 'foo' )->will_return( 5 );
		$this->assertEquals( 5, test_stub( 'foo' ) );
		$this->assertEquals( 7, test_stub( 'bar' ) );
	}

	public function test_stub_and_return_passed_arg_will_return_the_passed_argument_with_the_appropriate_index() {
		\Spies\mock_function( 'test_stub' )->and_return( \Spies\passed_arg( 1 ) );
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
