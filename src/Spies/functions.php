<?php
namespace Spies;

function stub_function( $function_name ) {
	return \Spies\Spy::stub_function( $function_name );
}

function mock_function( $function_name ) {
	return \Spies\Spy::stub_function( $function_name );
}

function get_spy_for( $function_name ) {
	return \Spies\Spy::get_spy_for( $function_name );
}

function expect_spy( $spy ) {
	return \Spies\Expectation::expect_spy( $spy );
}

function finish_spying() {
	try {
		\Spies\GlobalExpectations::resolve_delayed_expectations();
	} catch ( \Exception $e ) {
		\Spies\GlobalExpectations::clear_all_expectations();
		\Spies\GlobalSpies::clear_all_spies();
		\Spies\GlobalSpies::restore_original_global_functions();
		throw $e;
	}
	\Spies\GlobalExpectations::clear_all_expectations();
	\Spies\GlobalSpies::clear_all_spies();
	\Spies\GlobalSpies::restore_original_global_functions();
}

function mock_object() {
	return \Spies\MockObject::mock_object();
}

function mock_object_of( $class_name = null ) {
	return \Spies\MockObject::mock_object_of( $class_name );
}

function make_spy() {
	return new \Spies\Spy();
}

function any() {
	return new \Spies\AnyValue();
}

function match_array( $array ) {
	return new \Spies\MatchArray( $array );
}

function passed_arg( $index ) {
	return new \Spies\PassedArgument( $index );
}

function do_arrays_match( $array1, $array2 ) {
	return \Spies\Helpers::do_arrays_match( $array1, $array2 );
}
