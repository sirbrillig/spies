<?php
namespace Spies;

class MatchArray {
	public function __construct( $array ) {
		$this->expected_array = $array;
	}

	public function is_match( $actual ) {
		if ( ! is_array( $actual ) ) {
			return false;
		}
		$match_count = 0;
		$is_associative = $this->is_associative( $actual );
		foreach ( $this->expected_array as $key => $value ) {
			if ( $is_associative ) {
				// Compare associative arrays
				if ( array_key_exists( $key, $actual ) && $actual[ $key ] === $value ) {
					$match_count += 1;
				}
			} else {
				// Compare indexed arrays
				if ( ! $this->is_associative( $actual ) && in_array( $value, $actual ) ) {
					$match_count += 1;
				}
			}
		}
		return ( count( $this->expected_array ) === $match_count );
	}

	private function is_associative( $arr ) {
		if ( array() === $arr ) {
			return false;
		}
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

	public function __toString() {
		return 'MatchArray(' . json_encode( $this->expected_array ) . ')';
	}
}

