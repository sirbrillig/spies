<?php

/**
 * @runTestsInSeparateProcesses
 */
class MockObjectTest extends PHPUnit_Framework_TestCase {
	public function test_mock_object_returns_mock_object() {
		$mock = \Spies\mock_object();
		$this->assertTrue( $mock instanceof \Spies\MockObject );
	}

	public function test_add_method_returns_a_spy() {
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
}
