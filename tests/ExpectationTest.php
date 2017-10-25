<?php

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

	public function test__verify__throws_exception_on_fail() {
		// TODO: this will be the exception outside of PHPUnit; we need to be able to test both
		// $this->expectException( \Spies\UnmetExpectationException::class );
		$this->expectException( \PHPUnit_Framework_ExpectationFailedException::class );
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$expectation->verify();
	}

	public function test__to_have_been_called__reports_failure_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
		$this->assertContains( 'Failed asserting that a spy is called', $expectation->get_fail_message() );
	}

	public function test__not_to_have_been_called__reports_failure_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called();
		$spy();
		$this->assertContains( 'Failed asserting that a spy is not called', $expectation->get_fail_message() );
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

	public function test__times__is_met_if_spy_is_called_that_many_times() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$spy();
		$spy();
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__times__is_met_if_following__with() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo' )->times( 2 );
		$spy( 'foo' );
		$spy( 'foo' );
		$spy( 'bar' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__reports_fail_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo' );
		$spy( 'bar' );
		$this->assertContains( 'Failed asserting that a spy is called with arguments: ( "foo" )', $expectation->get_fail_message() );
	}

	public function test__times_with__reports_fail_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo' )->times( 2 );
		$spy( 'foo' );
		$spy( 'bar' );
		$this->assertContains( 'Failed asserting that a spy is called with arguments: ( "foo" ) 2 times', $expectation->get_fail_message() );
	}

	public function test__not_with__reports_fail_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called->with( 'foo' );
		$spy( 'foo' );
		$spy( 'bar' );
		$this->assertContains( 'Failed asserting that a spy is not called with arguments: ( "foo" )', $expectation->get_fail_message() );
	}

	public function test__not_times_with__reports_fail_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called->with( 'foo' )->times( 2 );
		$spy( 'foo' );
		$spy( 'foo' );
		$spy( 'bar' );
		$this->assertContains( 'Failed asserting that a spy is not called with arguments: ( "foo" )', $expectation->get_fail_message() );
	}

	public function test__times__is_not_met_if_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__times__is_not_met_if_spy_is_called_more_than_that_many_times() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->times( 2 );
		$spy();
		$spy();
		$spy();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__once__as_property_throws_an_error() {
		$this->expectException( \Spies\InvalidExpectationException::class );
		$spy = \Spies\make_spy();
		\Spies\expect_spy( $spy )->to_have_been_called->once;
	}

	public function test__once__is_met_if_spy_is_called_once() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$spy();
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__once__is_not_met_if_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__twice__as_property_throws_an_error() {
		$this->expectException( \Spies\InvalidExpectationException::class );
		$spy = \Spies\make_spy();
		\Spies\expect_spy( $spy )->to_have_been_called->twice;
	}

	public function test__once__is_not_met_if_spy_is_called_twice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->once();
		$spy();
		$spy();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__twice__is_met_if_spy_is_called_twice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$spy();
		$spy();
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__twice__is_not_met_if_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__twice__is_not_met_if_spy_is_called_thrice() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->twice();
		$spy();
		$spy();
		$spy();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__is_met_if_the_spy_is_called_with_the_same_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$spy( 'foo', 'bar' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__when__is_met_if_the_spy_is_called_and_function_returns_true() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function() {
			return true;
		} );
		$spy( 'foo', 'bar' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__when__reports_failure_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function() {
			return false;
		} );
		$spy( 'foo', 'bar' );
		$this->assertContains( 'Failed asserting that a spy is called with arguments matching the provided function', $expectation->get_fail_message() );
	}

	public function test__not_when__reports_failure_message_on_fail() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->not->to_have_been_called->when( function() {
			return true;
		} );
		$spy( 'foo', 'bar' );
		$this->assertContains( 'Failed asserting that a spy is not called with arguments matching the provided function', $expectation->get_fail_message() );
	}

	public function test__when__function_receives_arguments_for_each_call() {
		$found = [];
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function( $args ) use ( &$found ) {
			$found[] = $args;
			return true;
		} );
		$spy( 'foo', 'bar' );
		$spy( 'boo', 'far' );
		$this->assertTrue( $expectation->met_expectations() );
		$this->assertEquals( [ 'foo', 'bar' ], $found[0] );
		$this->assertEquals( [ 'boo', 'far' ], $found[1] );
	}

	public function test__when__is_not_met_if_the_spy_is_called_and_function_returns_false() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->when( function() {
			return false;
		} );
		$spy( 'foo', 'bar' );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__is_met_if_the_spy_is_called_and_function_returns_true() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( function() {
			return true;
		} );
		$spy( 'foo', 'bar' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__function_receives_arguments_for_each_call() {
		$found = [];
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( function( $args ) use ( &$found ) {
			$found[] = $args;
			return true;
		} );
		$spy( 'foo', 'bar' );
		$spy( 'boo', 'far' );
		$this->assertTrue( $expectation->met_expectations() );
		$this->assertEquals( [ 'foo', 'bar' ], $found[0] );
		$this->assertEquals( [ 'boo', 'far' ], $found[1] );
	}

	public function test__with__is_not_met_if_the_spy_is_called_and_function_returns_false() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( function() {
			return false;
		} );
		$spy( 'foo', 'bar' );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__and__match_pattern__is_met_if_the_spy_is_called_with_matching_pattern() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_pattern( '/Bart/i' ) );
		$spy( 'foo', 'slartibartfast' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__and__match_pattern__is_not_met_if_the_spy_is_called_with_differing_pattern() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_pattern( '/Bart/i' ) );
		$spy( 'foo', 'slartiblargfast' );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_met_if_the_spy_is_called_with_matching_key_values() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'bar', 'flim' => 'flam' ] ) );
		$spy( 'foo', [ 'bar' => 'baz', 'foo' => 'bar', 'flim' => 'flam' ] );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_met_if_the_spy_is_called_with_matching_key_values_for_exact_match() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'bar' ] ) );
		$spy( 'foo', [ 'foo' => 'bar' ] );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_met_if_the_spy_is_called_with_matching_index_keys() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'bar', 'foo' ] ) );
		$spy( 'foo', [ 'foo', 'bar', 'baz' ] );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_met_if_the_spy_is_called_with_matching_index_keys_for_exact_match() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'bar', 'foo' ] ) );
		$spy( 'foo', [ 'bar', 'foo' ] );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_not_met_if_the_spy_is_called_with_no_matching_index_keys() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'beep' ] ) );
		$spy( 'foo', [ 'foo', 'bar', 'baz' ] );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_not_met_if_the_spy_is_called_with_no_matching_key_values() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'baz' ] ) );
		$spy( 'foo', [ 'bar' => 'baz', 'foo' => 'bar', 'flim' => 'flam' ] );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__and__match_array__is_not_met_if_the_spy_is_called_with_some_matching_key_values() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\match_array( [ 'foo' => 'baz', 'flim' => 'flam' ] ) );
		$spy( 'foo', [ 'bar' => 'baz', 'foo' => 'bar', 'flim' => 'flam' ] );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__and__any__is_met_if_the_spy_is_called_with_any_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', \Spies\any() );
		$spy( 'foo', 'bar' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__with__is_not_met_if_the_spy_is_called_with_no_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$spy();
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__is_not_met_if_the_spy_is_called_with_different_arguments() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$spy( 'foo' );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__with__is_not_met_if_the_spy_is_not_called() {
		$spy = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'foo', 'bar' );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__before__is_met_if_the_spy_was_called_before_another_spy() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy_1 )->to_have_been_called->before( $spy_2 );
		$spy_1( 'foo' );
		$spy_2( 'bar' );
		$this->assertTrue( $expectation->met_expectations() );
	}

	public function test__before__reports_failure_message_on_fail() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy_1 )->to_have_been_called->before( $spy_2 );
		$spy_2( 'bar' );
		$spy_1( 'foo' );
		$this->assertContains( 'Failed asserting that a spy is called before a spy', $expectation->get_fail_message() );
	}

	public function test__before__is_not_met_if_the_spy_was_called_after_another_spy() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy_1 )->to_have_been_called->before( $spy_2 );
		$spy_2( 'bar' );
		$spy_1( 'foo' );
		$this->assertFalse( $expectation->met_expectations() );
	}

	public function test__not_before__reports_failure_message_on_fail() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$expectation = \Spies\expect_spy( $spy_1 )->not->to_have_been_called->before( $spy_2 );
		$spy_1( 'foo' );
		$spy_2( 'bar' );
		$this->assertContains( 'Failed asserting that a spy is not called before a spy', $expectation->get_fail_message() );
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
