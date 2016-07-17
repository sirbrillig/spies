<?php

class AssertionTest extends \Spies\TestCase {
	public function test_assert_was_called_is_true_when_called() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertSpyWasCalled( $spy );
	}

	public function test_assert_was_not_called_is_true_when_not_called() {
		$spy = \Spies\make_spy();
		$this->assertSpyWasNotCalled( $spy );
	}

	public function test_assert_that_was_called_is_true_when_called() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->wasCalled() );
	}

	public function test_assert_that_was_called_is_true_when_not_called() {
		$spy = \Spies\make_spy();
		$this->assertThat( $spy, $this->wasNotCalled() );
	}

	public function test_assert_that_logical_not_was_called_is_true_when_not_called() {
		$spy = \Spies\make_spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalled() ) );
	}

	public function test_assert_that_was_called_with_is_true_when_called_with_args() {
		$spy = \Spies\make_spy();
		$spy( 'a', 'b', 'c' );
		$this->assertThat( $spy, $this->wasCalledWith( [ 'a', 'b', 'c' ] ) );
	}

	public function test_assert_that_was_logical_not_called_with_is_true_when_not_called_with_args() {
		$spy = \Spies\make_spy();
		$spy( 'b', 'b', 'c' );
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledWith( [ 'a', 'b', 'c' ] ) ) );
	}

	public function test_assert_spy_was_called_with_is_true_when_called_with_args() {
		$spy = \Spies\make_spy();
		$spy( 'a', 'b', 'c' );
		$this->assertSpyWasCalledWith( $spy, [ 'a', 'b', 'c' ] );
	}
}
