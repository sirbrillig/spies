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
	\Spies\Expectation::resolve_delayed_expectations();
	\Spies\Expectation::clear_all_expectations();
	\Spies\Spy::clear_all_spies();
}

function mock_object() {
	return \Spies\MockObject::mock_object();
}
