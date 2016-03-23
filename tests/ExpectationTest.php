<?php

class ExpectationTest extends PHPUnit_Framework_TestCase {

	public function tearDown() {
		\Spies\finish_spying();
	}

	public function test_expect_spy_throws_an_error_if_called_without_a_spy() {
		$this->expectException( InvalidArgumentException::class );
		\Spies\expect_spy( 'foobar' );
	}

	public function test_expect_spy_returns_an_expectation() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy );
		$this->assertTrue( $expectation instanceof \Spies\Expectation );
	}

	public function test_to_have_been_called_is_not_met_if_spy_was_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$expectation->silent_failures = true;
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_to_have_been_called_is_met_if_spy_was_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$spy();
		$expectation->verify();
	}

	public function test_not_reverses_the_expectation() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called();
		$expectation->verify();
	}

	public function test_times_is_met_if_spy_is_called_that_many_times() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$spy();
		$spy();
		$expectation->verify();
	}

	public function test_times_is_not_met_if_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$expectation->silent_failures = true;
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_times_is_not_met_if_spy_is_called_more_than_that_many_times() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$expectation->silent_failures = true;
		$spy();
		$spy();
		$spy();
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_once_is_met_if_spy_is_called_once() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$spy();
		$expectation->verify();
	}

	public function test_once_is_not_met_if_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$expectation->silent_failures = true;
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_once_is_not_met_if_spy_is_called_twice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$expectation->silent_failures = true;
		$spy();
		$spy();
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_with_is_met_if_the_spy_is_called_with_the_same_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$spy( 'foo', 'bar' );
		$expectation->verify();
	}

	public function test_with_any_is_met_if_the_spy_is_called_with_any_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\any() );
		$spy( 'foo', 'bar' );
		$expectation->verify();
	}

	public function test_with_is_not_met_if_the_spy_is_called_with_no_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$expectation->silent_failures = true;
		$spy();
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_with_is_not_met_if_the_spy_is_called_with_different_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$expectation->silent_failures = true;
		$spy( 'foo' );
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_with_is_not_met_if_the_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$expectation->silent_failures = true;
		$this->assertInternalType( 'string', $expectation->verify() );
	}
}
