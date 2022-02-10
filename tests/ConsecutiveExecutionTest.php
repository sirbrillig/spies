<?php


function global_function_bar() {
	return - 1;
}

class ConsecutiveExecutionTest extends \Spies\TestCase {
	function tearDown() {
		\Spies\finish_spying();
	}

	public function test__undefined_function___can_be_stubbed() {
		$foo_spy = \Spies\get_spy_for( 'undefined_function' )->and_return( 4 );
		$ret     = undefined_function();
		$this->assertEquals( 1, $foo_spy->get_times_called() );
		$this->assertEquals( 4, $ret );
	}

	public function test__undefined_function___can_be_restubbed() {
		$foo_spy = \Spies\get_spy_for( 'undefined_function' );
		$ret     = undefined_function();
		$this->assertEquals( 1, $foo_spy->get_times_called() );
		$this->assertEquals( null, $ret );
	}

	public function test__undefined_function___can_be_restubbed_again() {
		$foo_spy = \Spies\get_spy_for( 'undefined_function' );
		$ret     = undefined_function();
		$this->assertEquals( 1, $foo_spy->get_times_called() );
		$this->assertEquals( null, $ret );
	}

	public function test__undefined_function___can_be_restubbed_again_with_new_return() {
		$foo_spy = \Spies\get_spy_for( 'undefined_function' )->and_return( 3 );
		$ret     = undefined_function();
		$this->assertEquals( 1, $foo_spy->get_times_called() );
		$this->assertEquals( 3, $ret );
	}

	public function test__global_function__can_be_stubbed() {
		$bar_spy = \Spies\get_spy_for( 'global_function_bar' )->and_return( 0 );
		$ret     = global_function_bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( 0, $ret );
	}

	public function test__global_function__can_be_restubbed() {
		$bar_spy = \Spies\get_spy_for( 'global_function_bar' );
		$ret     = global_function_bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( - 1, $ret );
	}

	public function test__global_function__can_be_restubbed_again() {
		$bar_spy = \Spies\get_spy_for( 'global_function_bar' );
		$ret     = global_function_bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( - 1, $ret );
	}

	public function test__global_function__can_be_restubbed_again_with_new_return() {
		$bar_spy = \Spies\get_spy_for( 'global_function_bar' )->and_return( 3 );
		$ret     = global_function_bar();
		$this->assertEquals( 1, $bar_spy->get_times_called() );
		$this->assertEquals( 3, $ret );
	}
}
