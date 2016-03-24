<?php
namespace Spies;

// Default Plugins
require( 'src/Spies/Plugins/WillReturn.php' );

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
	\Spies\GlobalExpectations::resolve_delayed_expectations();
	\Spies\GlobalExpectations::clear_all_expectations();
	\Spies\GlobalSpies::clear_all_spies();
}

function mock_object() {
	return \Spies\MockObject::mock_object();
}

function make_spy() {
	return new \Spies\Spy();
}

function any() {
	return new \Spies\AnyValue();
}

function passed_arg( $index ) {
	return new \Spies\PassedArgument( $index );
}
