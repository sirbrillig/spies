<?php

if ( ! function_exists( 'mock_function' ) ) {
	function mock_function( $function_name ) {
		return \Spies\Spy::stub_function( $function_name );
	}
}

if ( ! function_exists( 'get_spy_for' ) ) {
	function get_spy_for( $function_name ) {
		return \Spies\Spy::get_spy_for( $function_name );
	}
}

if ( ! function_exists( 'expect_spy' ) ) {
	function expect_spy( $spy ) {
		return \Spies\Expectation::expect_spy( $spy );
	}
}

if ( ! function_exists( 'finish_spying' ) ) {
	function finish_spying() {
		return \Spies\Expectation::finish_spying();
	}
}

if ( ! function_exists( 'mock_object' ) ) {
	function mock_object() {
		return \Spies\MockObject::mock_object();
	}
}
