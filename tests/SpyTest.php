<?php
function globalFunctionFoo() {
	return 'bar';
}

function globalFunctionBar( $val ) {
	return $val;
}

/**
 * @runTestsInSeparateProcesses
 */
class SpyTest extends PHPUnit_Framework_TestCase {
	public function test_spy_was_called_returns_true_if_called() {
		$spy = new \Spies\Spy();
		$spy();
		$this->assertTrue( $spy->was_called() );
	}

	public function test_make_spy_was_called_returns_true_if_called() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertTrue( $spy->was_called() );
	}

	public function test_spy_was_called_returns_false_if_not_called() {
		$spy = \Spies\make_spy();
		$this->assertFalse( $spy->was_called() );
	}

	public function test_get_spy_for_creates_a_global_function() {
		\Spies\get_spy_for( 'test_spy' );
		$this->assertTrue( function_exists( 'test_spy' ) );
	}

	public function test_get_spy_for_returns_a_spy() {
		$spy = \Spies\get_spy_for( 'test_spy' );
		$this->assertTrue( $spy instanceof \Spies\Spy );
	}

	public function test_get_spy_for_creates_a_spy_that_is_called_when_the_global_function_is_called() {
		$spy = \Spies\get_spy_for( 'test_spy' );
		test_spy();
		$this->assertTrue( $spy->was_called() );
	}

	public function test_if_get_spy_for_is_called_twice_with_the_same_string_it_returns_the_same_spy() {
		$spy_1 = \Spies\get_spy_for( 'test_spy' );
		\Spies\finish_spying();
		$spy_2 = \Spies\get_spy_for( 'test_spy' );
		$this->assertEquals( $spy_1, $spy_2 );
	}

	public function test_clear_call_record_clears_the_call_record_for_a_spy() {
		$spy = \Spies\get_spy_for( 'test_spy' );
		test_spy();
		$spy->clear_call_record();
		$this->assertFalse( $spy->was_called() );
	}

	public function test_if_get_spy_for_is_called_again_after_clearing_spies_it_resets_the_call_record_for_the_spy() {
		\Spies\get_spy_for( 'test_spy' );
		test_spy();
		\Spies\finish_spying();
		$spy = \Spies\get_spy_for( 'test_spy' );
		$this->assertFalse( $spy->was_called() );
	}

	public function test_arguments_passed_to_a_spy_are_saved_in_the_call_record() {
		$spy = \Spies\make_spy();
		$spy( 'foo', 'bar' );
		$spy( 'baz', 'bar' );
		$call_record_arguments = array_map( function( $call ) {
			return $call->get_args();
		}, $spy->get_called_functions() );
		$this->assertContains( [ 'foo', 'bar' ], $call_record_arguments );
		$this->assertContains( [ 'baz', 'bar' ], $call_record_arguments );
	}

	public function test_spy_was_called_times_returns_true_for_the_number_of_times_the_spy_was_called() {
		$spy = \Spies\make_spy();
		$spy();
		$spy();
		$spy();
		$this->assertTrue( $spy->was_called_times( 3 ) );
	}

	public function test_spy_was_called_times_returns_false_if_the_argument_does_not_match_the_number_of_times_the_spy_was_called() {
		$spy = \Spies\make_spy();
		$spy();
		$spy();
		$spy();
		$this->assertFalse( $spy->was_called_times( 6 ) );
	}

	public function test_spy_was_called_with_array_returns_true_if_the_spy_was_called_with_the_arguments_provided() {
		$spy = \Spies\make_spy();
		$spy( 'foo', 'bar', 'baz' );
		$this->assertTrue( $spy->was_called_with_array( [ 'foo', 'bar', 'baz' ] ) );
	}

	public function test_spy_was_called_with_array_returns_false_if_the_spy_was_not_called_with_the_arguments_provided() {
		$spy = \Spies\make_spy();
		$spy( 'foo' );
		$this->assertFalse( $spy->was_called_with_array( [ 'foo', 'bar', 'baz' ] ) );
	}

	public function test_spy_was_called_with_returns_true_if_the_spy_was_called_with_the_arguments_provided() {
		$spy = \Spies\make_spy();
		$spy( 'foo', 'bar', 'baz' );
		$this->assertTrue( $spy->was_called_with( 'foo', 'bar', 'baz' ) );
	}

	public function test_spy_was_called_with_returns_true_if_the_spy_was_called_with_the_object_arguments_provided() {
		$spy = \Spies\make_spy();
		$obj = (object) [ 'ID' => 5 ];
		$spy( $obj );
		$this->assertTrue( $spy->was_called_with( $obj ) );
	}

	public function test_spy_was_called_with_returns_true_if_the_spy_was_called_with_the_deep_object_arguments_provided() {
		$spy = \Spies\make_spy();
		$obj = [ (object) [ 'ID' => 5, 'names' => [ 'bob' ], 'colors' => [ 'red' => '#f00' ] ] ];
		$spy( [ 'foo' => $obj[0] ] );
		$this->assertTrue( $spy->was_called_with( [ 'foo' => $obj[0] ] ) );
	}

	public function test_spy_was_called_with_returns_false_if_the_spy_was_not_called_with_the_arguments_provided() {
		$spy = \Spies\make_spy();
		$spy( 'foo' );
		$this->assertFalse( $spy->was_called_with( 'foo', 'bar', 'baz' ) );
	}

	public function test_spy_was_called_when_returns_true_if_the_spy_was_called_when_the_function_returns_true() {
		$spy = \Spies\make_spy();
		$spy( 'foo', 'bar', 'baz' );
		$this->assertTrue( $spy->was_called_when( function() {
			return true;
		} ) );
	}

	public function test_spy_was_called_when_gets_the_arguments_for_each_call() {
		$spy = \Spies\make_spy();
		$spy( 'foo', 'bar' );
		$spy( 'boo', 'far' );
		$found = [];
		$spy->was_called_when( function( $args ) use ( &$found ) {
			$found[] = $args;
			return true;
		} );
		$this->assertEquals( [ 'foo', 'bar' ], $found[0] );
		$this->assertEquals( [ 'boo', 'far' ], $found[1] );
	}

	public function test_spy_was_called_when_returns_false_if_the_spy_was_called_when_the_function_returns_false() {
		$spy = \Spies\make_spy();
		$spy( 'foo', 'bar', 'baz' );
		$this->assertFalse( $spy->was_called_when( function() {
			return false;
		} ) );
	}

	public function test_spy_was_called_when_tests_object_arguments_by_reference() {
		$spy = \Spies\make_spy();
		$obj = new \StdClass();
		$obj->foo = 'original';
		$spy( $obj );
		$obj->foo = 'modified';
		$this->assertTrue( $spy->was_called_when( function( $args ) {
			return $args[0]->foo === 'modified';
		} ) );
	}

	public function test_spy_was_called_when_tests_array_arguments_by_value() {
		$spy = \Spies\make_spy();
		$obj = [ 'foo' => 'original' ];
		$spy( $obj );
		$obj['foo'] = 'modified';
		$this->assertTrue( $spy->was_called_when( function( $args ) {
			return $args[0]['foo'] === 'original';
		} ) );
	}

	public function test_spy_was_called_before_returns_true_if_called_before_target_spy() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$spy_1( 'foo' );
		$spy_2( 'bar' );
		$this->assertTrue( $spy_1->was_called_before( $spy_2 ) );
	}

	public function test_spy_was_called_before_returns_false_if_called_after_target_spy() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$spy_2( 'bar' );
		$spy_1( 'foo' );
		$this->assertFalse( $spy_1->was_called_before( $spy_2 ) );
	}

	public function test_spy_was_called_before_returns_false_if_the_first_spy_was_not_called() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$spy_2( 'bar' );
		$this->assertFalse( $spy_1->was_called_before( $spy_2 ) );
	}

	public function test_spy_was_called_before_returns_false_if_the_second_spy_was_not_called() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$spy_1( 'bar' );
		$this->assertFalse( $spy_1->was_called_before( $spy_2 ) );
	}

	public function test_spy_was_called_before_returns_true_if_called_before_target_spy_even_if_called_again_later() {
		$spy_1 = \Spies\make_spy();
		$spy_2 = \Spies\make_spy();
		$spy_1( 'foo' );
		$spy_2( 'bar' );
		$spy_1( 'foo' );
		$this->assertTrue( $spy_1->was_called_before( $spy_2 ) );
	}

	public function test_calling_global_function_after_finish_spying_throws_an_exception() {
		$this->expectException( \Spies\UndefinedFunctionException::class );
		\Spies\get_spy_for( 'my_undefined_global' );
		\Spies\finish_spying();
		my_undefined_global();
	}

	public function test_get_times_called_returns_times_called() {
		$spy = \Spies\make_spy();
		$spy();
		$spy();
		$spy();
		$this->assertEquals( 3, $spy->get_times_called() );
	}

	public function test_get_call_returns_the_call_record_for_that_call_index() {
		$spy = \Spies\make_spy();
		$spy( 'a' );
		$spy( 'b' );
		$spy( 'c' );
		$this->assertEquals( [ 'b' ], $spy->get_call( 1 )->get_args() );
	}

	public function test_spy_was_called_times_with_returns_true_if_called_with_those_args_that_number_of_times() {
		$spy = \Spies\make_spy();
		$spy( 'a', 'b' );
		$spy( 'a', 'b' );
		$spy( 'b', 'b' );
		$this->assertTrue( $spy->was_called_times_with( 2, 'a', 'b' ) );
	}

	public function test_spy_was_called_times_with_returns_false_if_not_called_with_those_args_that_number_of_times() {
		$spy = \Spies\make_spy();
		$spy( 'a', 'b' );
		$spy( 'b', 'b' );
		$this->assertFalse( $spy->was_called_times_with( 2, 'a', 'b' ) );
	}

	public function test_stub_on_internal_existing_method_replaces_return_value_of_existing_method() {
		\Spies\mock_function( 'trigger_error' )->and_return( 'boo' );
		$this->assertEquals( 'boo', trigger_error( 'foo' ) );
	}

	public function test_stub_on_internal_existing_method_replaces_return_value_of_existing_method_if_null() {
		\Spies\mock_function( 'trigger_error' )->and_return( null );
		$this->assertEquals( null, trigger_error( 'foo' ) );
	}

	public function test_spy_on_internal_existing_method_calls_original_method_when_calling_spy() {
		$spy = \Spies\get_spy_for( 'count' );
		$this->assertEquals( 2, $spy( [ 'a', 'b' ] ) );
	}

	public function test_stub_on_existing_method_replaces_return_value_of_spy() {
		$spy = \Spies\mock_function( 'globalFunctionFoo' )->and_return( 'boo' );
		$this->assertEquals( 'boo', $spy() );
	}

	public function test_stub_on_existing_method_replaces_return_value_of_existing_method() {
		\Spies\mock_function( 'globalFunctionFoo' )->and_return( 'boo' );
		$this->assertEquals( 'boo', globalFunctionFoo() );
	}

	public function test_spy_on_existing_method_calls_original_method_when_calling_spy() {
		$spy = \Spies\get_spy_for( 'globalFunctionFoo' );
		$this->assertEquals( 'bar', $spy() );
	}

	public function test_spy_on_existing_method_registers_calls_of_spy() {
		$spy = \Spies\get_spy_for( 'globalFunctionBar' );
		$spy( 'hi' );
		$this->assertTrue( $spy->was_called_with( 'hi' ) );
	}

	public function test_spy_on_existing_method_does_not_modify_return_value_of_orignal_method() {
		\Spies\get_spy_for( 'globalFunctionBar' );
		$ret = globalFunctionBar( 'original' );
		$this->assertEquals( 'original', $ret );
	}

	public function test_spy_on_existing_method_registers_calls_of_existing_method() {
		$spy = \Spies\get_spy_for( 'globalFunctionBar' );
		globalFunctionBar( 'original' );
		$this->assertTrue( $spy->was_called_with( 'original' ) );
	}

	public function test_spy_on_existing_method_registers_calls_of_existing_method_when_called_multiple_times() {
		$spy = \Spies\get_spy_for( 'globalFunctionBar' );
		globalFunctionBar( 'foo' );
		globalFunctionBar( 'original' );
		$this->assertTrue( $spy->was_called_with( 'original' ) );
	}

	public function test_calling_existing_method_after_finish_spying_does_not_call_stub() {
		\Spies\mock_function( 'globalFunctionFoo' )->and_return( 'boo' );
		\Spies\finish_spying();
		$this->assertEquals( 'bar', globalFunctionFoo() );
	}
}
