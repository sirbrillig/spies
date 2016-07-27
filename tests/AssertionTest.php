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

	public function test_assert_spy_was_not_called_with_is_true_when_not_called_with_args() {
		$spy = \Spies\make_spy();
		$spy( 'e', 'b', 'c' );
		$this->assertSpyWasNotCalledWith( $spy, [ 'a', 'b', 'c' ] );
	}

	public function test_assert_was_called_times_is_true_when_called_once() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->wasCalledTimes( 1 ) );
	}

	public function test_assert_was_called_times_is_true_when_called_twice() {
		$spy = \Spies\make_spy();
		$spy();
		$spy();
		$this->assertThat( $spy, $this->wasCalledTimes( 2 ) );
	}

	public function test_assert_was_called_times_is_false_when_called_less_than_number() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledTimes( 2 ) ) );
	}

	public function test_assert_was_called_times_is_false_when_called_more_than_number() {
		$spy = \Spies\make_spy();
		$spy();
		$spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledTimes( 1 ) ) );
	}

	public function test_assert_spy_was_called_times_is_true_when_called_that_number() {
		$spy = \Spies\make_spy();
		$spy();
		$spy();
		$spy();
		$this->assertSpyWasCalledTimes( $spy, 3 );
	}

	public function test_assert_spy_was_not_called_times_is_true_when_not_called_that_number() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertSpyWasNotCalledTimes( $spy, 3 );
	}

	public function test_assert_that_spy_was_called_before_is_true_when_called_before_other_spy() {
		$spy = \Spies\make_spy();
		$spy2 = \Spies\make_spy();
		$spy();
		$spy2();
		$this->assertThat( $spy, $this->wasCalledBefore( $spy2 ) );
	}

	public function test_assert_that_spy_was_called_before_is_false_when_called_after_other_spy() {
		$spy = \Spies\make_spy();
		$spy2 = \Spies\make_spy();
		$spy2();
		$spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledBefore( $spy2 ) ) );
	}

	public function test_assert_that_spy_was_called_before_is_false_when_other_spy_not_called() {
		$spy = \Spies\make_spy();
		$spy2 = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledBefore( $spy2 ) ) );
	}

	public function test_assert_that_spy_was_called_before_is_false_when_spy_not_called() {
		$spy = \Spies\make_spy();
		$spy2 = \Spies\make_spy();
		$spy2();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledBefore( $spy2 ) ) );
	}

	public function test_assert_spy_was_called_before_is_true_when_called_before_other_spy() {
		$spy = \Spies\make_spy();
		$spy2 = \Spies\make_spy();
		$spy();
		$spy2();
		$this->assertSpyWasCalledBefore( $spy, $spy2 );
	}

	public function test_assert_spy_was_not_called_before_is_true_when_called_after_other_spy() {
		$spy = \Spies\make_spy();
		$spy2 = \Spies\make_spy();
		$spy2();
		$spy();
		$this->assertSpyWasNotCalledBefore( $spy, $spy2 );
	}

	public function test_assert_that_spy_was_called_when_is_true_when_called_function_returns_true() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->wasCalledWhen( function() {
			return true;
		} ) );
	}

	public function test_assert_that_spy_was_called_when_is_false_when_called_function_returns_false() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledWhen( function() {
			return false;
		} ) ) );
	}

	public function test_assert_that_spy_was_called_when_is_false_when_spy_was_not_called() {
		$spy = \Spies\make_spy();
		$this->assertThat( $spy, $this->logicalNot( $this->wasCalledWhen( function() {
			return true;
		} ) ) );
	}

	public function test_assert_that_spy_was_called_when_function_is_called_with_spy_args() {
		$spy = \Spies\make_spy();
		$spy( 'hello', 'world' );
		$this->assertThat( $spy, $this->wasCalledWhen( function( $args ) {
			return ( $args === [ 'hello', 'world' ] );
		} ) );
	}

	public function test_assert_that_spy_was_called_when_function_is_called_for_each_call() {
		$spy = \Spies\make_spy();
		$spy( 1 );
		$spy( 2 );
		$count = 0;
		$this->assertThat( $spy, $this->wasCalledWhen( function() use ( &$count ) {
			$count ++;
			return true;
		} ) );
		$this->assertEquals( 2, $count );
	}

	public function test_assert_spy_was_called_when_is_true_when_called_function_returns_true() {
		$spy = \Spies\make_spy();
		$spy( 'hi' );
		$spy( 'yo' );
		$this->assertSpyWasCalledWhen( $spy, function( $args ) {
			return ( $args === [ 'yo' ] );
		} );
	}

	public function test_assert_spy_was_called_when_is_false_when_called_function_returns_false() {
		$spy = \Spies\make_spy();
		$spy();
		$spy( 'yo' );
		$this->assertSpyWasNotCalledWhen( $spy, function( $args ) {
			return ( $args === [ 'hi' ] );
		} );
	}
}
