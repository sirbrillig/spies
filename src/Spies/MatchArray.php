<?php
namespace Spies;

class MatchArray {
	public function __construct( $array ) {
		$this->array_to_match = $array;
	}

	public function is_match( $actual ) {
		if ( ! is_array( $actual ) ) {
			return false;
		}
		foreach ( $this->array_to_match as $key => $value ) {
			if ( array_key_exists( $key, $actual ) && $actual[ $key ] === $value ) {
				return true;
			}
		}
		return false;
	}
}

