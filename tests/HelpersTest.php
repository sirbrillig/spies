<?php

class HelpersTest extends \Spies\TestCase {
	public function test_do_arrays_match_returns_true_if_arrays_are_the_same() {
		$array1 = [ 'a', 'b', 'c' ];
		$array2 = [ 'a', 'b', 'c' ];
		$this->assertTrue( \Spies\do_arrays_match( $array1, $array2 ) );
	}

	public function test_do_arrays_match_returns_false_if_arrays_are_different() {
		$array1 = [ 'a', 'b', 'c' ];
		$array2 = [ 'b', 'c' ];
		$this->assertFalse( \Spies\do_arrays_match( $array1, $array2 ) );
	}

	public function test_do_arrays_match_returns_true_if_using_match_array_and_arrays_match() {
		$array1 = [ 'a', 'b', 'c' ];
		$array2 = [ 'b', 'c' ];
		$this->assertTrue( \Spies\do_arrays_match( $array1, \Spies\match_array( $array2 ) ) );
	}

	public function test_do_arrays_match_returns_false_if_using_match_array_and_arrays_differ() {
		$array1 = [ 'a', 'b', 'c' ];
		$array2 = [ 'd', 'c' ];
		$this->assertFalse( \Spies\do_arrays_match( $array1, \Spies\match_array( $array2 ) ) );
	}
}

