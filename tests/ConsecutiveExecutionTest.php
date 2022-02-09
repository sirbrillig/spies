<?php


function bar() {
	return - 1;
}


class ConsecutiveExecutionTest extends \Spies\TestCase {
	function tearDown() {
		\Spies\finish_spying();
	}

	public function test_global_function_can_be_stubbed() {
		$bar_spy = \Spies\get_spy_for( 'bar' )->and_return( 0 );
		$ret     = bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( 0, $ret );
	}

	public function test_global_function_can_be_restubbed() {
		$bar_spy = \Spies\get_spy_for( 'bar' );
		$ret     = bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( - 1, $ret );
	}

	public function test_global_function_can_be_restubbed_again() {
		$bar_spy = \Spies\get_spy_for( 'bar' );
		$ret     = bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( - 1, $ret );
	}

	public function test_global_function_can_be_restubbed_again_with_new_return() {
		$bar_spy = \Spies\get_spy_for( 'bar' )->and_return( 3 );
		$ret     = bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( 3, $ret );
	}
}
