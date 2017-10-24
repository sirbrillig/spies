<?php

/**
 * @runTestsInSeparateProcesses
 */
class ExpectationTest extends PHPUnit_Framework_TestCase {

	public function tearDown() {
		\Spies\finish_spying();
	}

	public function test__expect_spy__throws_an_error_if_called_without_a_spy() {
		$this->expectException( InvalidArgumentException::class );
		\Spies\expect_spy( 'foobar' );
	}

	public function test__expect_spy__returns_an_expectation() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy );
		$this->assertTrue( $expectation instanceof \Spies\Expectation );
	}

	public function test__to_have_been_called__is_not_met_if_spy_was_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__to_have_been_called__reports_failure_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$this->assertEquals( 'Failed asserting that a spy is called', $expectation->get_fail_message() );
	}

	public function test__to_have_been_called__is_met_if_spy_was_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$spy();
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__not__reverses_an_expectation() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called();
		$this->assertTrue( $expectation->met_expectations() );
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
		$this->assertFalse( $expectation->verify() );
	}

	public function test_times_is_not_met_if_spy_is_called_more_than_that_many_times() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$expectation->silent_failures = true;
		$spy();
		$spy();
		$spy();
		$this->assertFalse( $expectation->verify() );
	}

	public function test_once_as_property_throws_an_error() {
		$this->expectException( \Spies\InvalidExpectationException::class );
		$spy = \Spies\make_spy();
		\Spies\expect_spy( $spy )->to_have_been_called->once;
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
		$this->assertFalse( $expectation->verify() );
	}

	public function test_twice_as_property_throws_an_error() {
		$this->expectException( \Spies\InvalidExpectationException::class );
		$spy = \Spies\make_spy();
		\Spies\expect_spy( $spy )->to_have_been_called->twice;
	}

	public function test_once_is_not_met_if_spy_is_called_twice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$expectation->silent_failures = true;
		$spy();
		$spy();
		$this->assertFalse( $expectation->verify() );
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
		$this->assertFalse( $expectation->verify() );
	}

	public function test_twice_is_not_met_if_spy_is_called_thrice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$expectation->silent_failures = true;
		$spy();
		$spy();
		$spy();
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_is_met_if_the_spy_is_called_with_the_same_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$spy( 'foo', 'bar' );
		$expectation->verify();
	}

	public function test_when_is_met_if_the_spy_is_called_and_function_returns_true() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function() {
			return true;
		} );
		$spy( 'foo', 'bar' );
		$expectation->verify();
	}

	public function test_when_function_receives_arguments_for_each_call() {
		$found = [];
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function( $args ) use ( &$found ) {
			$found[] = $args;
			return true;
		} );
		$spy( 'foo', 'bar' );
		$spy( 'boo', 'far' );
		$expectation->verify();
		$this->assertEquals( [ 'foo', 'bar' ], $found[0] );
		$this->assertEquals( [ 'boo', 'far' ], $found[1] );
	}

	public function test_when_is_not_met_if_the_spy_is_called_and_function_returns_false() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function() {
			return false;
		} );
		$expectation->silent_failures = true;
		$spy( 'foo', 'bar' );
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_is_met_if_the_spy_is_called_and_function_returns_true() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( function() {
			return true;
		} );
		$spy( 'foo', 'bar' );
		$expectation->verify();
	}

	public function test_with_function_receives_arguments_for_each_call() {
		$found = [];
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( function( $args ) use ( &$found ) {
			$found[] = $args;
			return true;
		} );
		$spy( 'foo', 'bar' );
		$spy( 'boo', 'far' );
		$expectation->verify();
		$this->assertEquals( [ 'foo', 'bar' ], $found[0] );
		$this->assertEquals( [ 'boo', 'far' ], $found[1] );
	}

	public function test_with_is_not_met_if_the_spy_is_called_and_function_returns_false() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( function() {
			return false;
		} );
		$expectation->silent_failures = true;
		$spy( 'foo', 'bar' );
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_match_pattern_is_met_if_the_spy_is_called_with_matching_pattern() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_pattern( '/Bart/i' ) );
		$spy( 'foo', 'slartibartfast' );
		$expectation->verify();
	}

	public function test_with_match_pattern_is_not_met_if_the_spy_is_called_with_differing_pattern() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_pattern( '/Bart/i' ) );
		$expectation->silent_failures = true;
		$spy( 'foo', 'slartiblargfast' );
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_match_array_is_met_if_the_spy_is_called_with_matching_key_values() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'bar', 'flim' => 'flam' ] ) );
		$spy( 'foo', [ 'bar' => 'baz', 'foo' => 'bar', 'flim' => 'flam' ] );
		$expectation->verify();
	}

	public function test_with_match_array_is_met_if_the_spy_is_called_with_matching_key_values_for_exact_match() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'bar' ] ) );
		$spy( 'foo', [ 'foo' => 'bar' ] );
		$expectation->verify();
	}

	public function test_with_match_array_is_met_if_the_spy_is_called_with_matching_index_keys() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'bar', 'foo' ] ) );
		$spy( 'foo', [ 'foo', 'bar', 'baz' ] );
		$expectation->verify();
	}

	public function test_with_match_array_is_met_if_the_spy_is_called_with_matching_index_keys_for_exact_match() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'bar', 'foo' ] ) );
		$spy( 'foo', [ 'bar', 'foo' ] );
		$expectation->verify();
	}

	public function test_with_match_array_is_not_met_if_the_spy_is_called_with_no_matching_index_keys() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'beep' ] ) );
		$expectation->silent_failures = true;
		$spy( 'foo', [ 'foo', 'bar', 'baz' ] );
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_match_array_is_not_met_if_the_spy_is_called_with_no_matching_key_values() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'baz' ] ) );
		$expectation->silent_failures = true;
		$spy( 'foo', [ 'bar' => 'baz', 'foo' => 'bar', 'flim' => 'flam' ] );
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_match_array_is_not_met_if_the_spy_is_called_with_some_matching_key_values() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'baz', 'flim' => 'flam' ] ) );
		$expectation->silent_failures = true;
		$spy( 'foo', [ 'bar' => 'baz', 'foo' => 'bar', 'flim' => 'flam' ] );
		$this->assertFalse( $expectation->verify() );
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
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_is_not_met_if_the_spy_is_called_with_different_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$expectation->silent_failures = true;
		$spy( 'foo' );
		$this->assertFalse( $expectation->verify() );
	}

	public function test_with_is_not_met_if_the_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$expectation->silent_failures = true;
		$this->assertFalse( $expectation->verify() );
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
		$this->assertFalse( $expectation->verify() );
	}

	public function test_global_expectation_is_cleared_by_finish_spying() {
		$spy = \Spies\get_spy_for( 'test_func' );
		\Spies\expect_spy( $spy )->to_have_been_called->with( 'first call' );
		test_func( 'first call' );
		\Spies\finish_spying();
		$spy = \Spies\get_spy_for( 'test_func' );
		\Spies\expect_spy( $spy )->not->to_have_been_called->with( 'first call' );
	}
}
