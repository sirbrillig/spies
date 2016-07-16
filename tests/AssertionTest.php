<?php

class AssertionTest extends \Spies\TestCase {
	public function test_assert_was_called_is_true_when_called() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertSpyWasCalled( $spy );
	}

	public function test_assert_was_not_called_is_false_when_not_called() {
		$spy = \Spies\make_spy();
		$this->assertSpyWasNotCalled( $spy );
	}

	public function test_assert_that_was_called_is_true_when_called() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->wasCalled() );
	}

	public function test_assert_that_was_called_is_false_when_not_called() {
		$spy = \Spies\make_spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalled() ) );
	}
}
