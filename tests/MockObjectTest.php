<?php

class Greeter {
	public function say_hello() {
		return 'hello';
	}

	public function say_goodbye() {
		return 'goodbye';
	}
}

/**
 * @runTestsInSeparateProcesses
 */
class MockObjectTest extends PHPUnit_Framework_TestCase {
	public function test_mock_object_returns_mock_object() {
		$mock = \Spies\mock_object();
		$this->assertTrue( $mock instanceof \Spies\MockObject );
	}

	public function test_add_method_with_a_function_uses_that_function_as_the_method() {
		$adder = \Spies\mock_object();
		$func = function( $val ) {
			return $val - 1;
		};
		$adder->add_method( 'subtract', $func );
		$this->assertEquals( 2, $adder->subtract( 3 ) );
	}

	public function test_add_method_with_a_function_returns_that_function() {
		$adder = \Spies\mock_object();
		$func = function( $val ) {
			return $val + 1;
		};
		$add_one = $adder->add_method( 'add_one', $func );
		$this->assertEquals( 4, $add_one( 3 ) );
	}

	public function test_add_method_without_a_second_argument_returns_a_spy() {
		$adder = \Spies\mock_object();
		$add_one = $adder->add_method( 'add_one' );
		$this->assertTrue( $add_one instanceof \Spies\Spy );
	}

	public function test_add_method_adds_a_method_to_the_mock_object() {
		$adder = \Spies\mock_object();
		$add_one = $adder->add_method( 'add_one' );
		$adder->add_one( 4 );
		$this->assertTrue( $add_one->was_called_with( 4 ) );
	}

	public function test_mock_object_of_adds_spies_for_each_method_on_the_target() {
		$mock = \Spies\mock_object_of( 'Greeter' );
		$this->assertEquals( null, $mock->say_hello() );
	}

	public function test_mock_object_of_allow_overriding_methods() {
		$mock = \Spies\mock_object_of( 'Greeter' );
		$mock->add_method( 'say_hello' )->that_returns( 'greetings' );
		$this->assertEquals( 'greetings', $mock->say_hello() );
		$this->assertEquals( null, $mock->say_goodbye() );
	}

	public function test_spy_on_method_is_an_alias_for_add_method() {
		$mock = \Spies\mock_object_of( 'Greeter' );
		$mock->spy_on_method( 'say_hello' )->that_returns( 'greetings' );
		$this->assertEquals( 'greetings', $mock->say_hello() );
		$this->assertEquals( null, $mock->say_goodbye() );
	}

	public function test_mock_object_throws_error_when_unmocked_method_is_called() {
		$this->setExpectedException( '\Spies\UndefinedFunctionException' );
		$mock = \Spies\mock_object();
		$mock->foobar();
	}

	public function test_mock_object_with_ignore_missing_does_not_throw_error_when_unmocked_method_is_called() {
		$mock = \Spies\mock_object()->and_ignore_missing();
		$mock->foobar();
	}

	public function test_mock_object_with_two_calls_to_add_method_stubs_both_methods() {
		$mock = \Spies\mock_object();
		$mock->add_method( 'test_stub' )->when_called->with( 'foo' )->will_return( 5 );
		$mock->add_method( 'test_stub' )->when_called->with( 'bar' )->will_return( 6 );
		$this->assertEquals( 5, $mock->test_stub( 'foo' ) );
		$this->assertEquals( 6, $mock->test_stub( 'bar' ) );
	}

	public function test_mock_object_with_two_calls_to_add_method_allows_a_default() {
		$mock = \Spies\mock_object();
		$mock->add_method( 'test_stub' )->when_called->will_return( 5 );
		$mock->add_method( 'test_stub' )->when_called->with( 'bar' )->will_return( 6 );
		$this->assertEquals( 5, $mock->test_stub( 'hello' ) );
		$this->assertEquals( 6, $mock->test_stub( 'bar' ) );
	}
}
