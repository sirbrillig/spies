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

	public function test_failure_without_not_does_not_use_not_in_the_message() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$expectation->silent_failures = true;
		$this->assertNotContains( 'not to be called', $expectation->verify() );
	}

	public function test_failure_with_not_uses_not_in_the_message() {
		$spy = \Spies\make_spy();
		$spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called();
		$expectation->silent_failures = true;
		$this->assertContains( 'not to be called', $expectation->verify() );
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

	public function test_times_is_met_if_following_with() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo' )->times( 2 );
		$spy( 'foo' );
		$spy( 'foo' );
		$spy( 'bar' );
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

	public function test_twice_is_met_if_spy_is_called_twice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$spy();
		$spy();
		$expectation->verify();
	}

	public function test_twice_is_not_met_if_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$expectation->silent_failures = true;
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_twice_is_not_met_if_spy_is_called_thrice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$expectation->silent_failures = true;
		$spy();
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

	public function test_before_is_met_if_the_spy_was_called_before_another_spy() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy_1 )->to_have_been_called->before( $spy_2 );
		$spy_1( 'foo' );
		$spy_2( 'bar' );
		$expectation->verify();
	}

	public function test_before_is_not_met_if_the_spy_was_called_after_another_spy() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy_1 )->to_have_been_called->before( $spy_2 );
		$spy_2( 'bar' );
		$spy_1( 'foo' );
		$expectation->silent_failures = true;
		$this->assertInternalType( 'string', $expectation->verify() );
	}

	public function test_global_expectation_is_cleared_by_finish_spying() {
		$spy = \Spies\get_spy_for( 'test_func' );
		\Spies\expect_spy( $spy )->to_have_been_called->with( 'first call' );
		test_func( 'first call' );
		\Spies\finish_spying();
		$spy = \Spies\get_spy_for( 'test_func' );
		\Spies\expect_spy( $spy )->not->to_have_been_called->with( 'first call' );
	}

	public function test_throw_exceptions_causes_spy_to_throw_exceptions_on_failure() {
		$this->expectException( \Spies\UnmetExpectationException::class );
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$expectation->throw_exceptions = true;
		$expectation->verify();
	}

	public function test_throw_exceptions_causes_spy_to_throw_exceptions_on_failure_with_finish_spying() {
		$this->expectException( \Spies\UnmetExpectationException::class );
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$expectation->throw_exceptions = true;
		\Spies\finish_spying();
	}
}
