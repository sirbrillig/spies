<?php
namespace Spies;

class MatchPattern {
	public function __construct( $pattern ) {
		$this->expected_pattern = $pattern;
	}

	public function is_match( $actual ) {
		if ( ! is_string( $actual ) ) {
			return false;
		}
		// Convert 0 and FALSE to false and 1 to true
		if ( preg_match( $this->expected_pattern, $actual ) ) {
			return true;
		}
		return false;
	}

	public function __toString() {
		return "MatchPattern('{$this->expected_pattern}')";
	}
}
