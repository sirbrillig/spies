<?php

namespace Spies;

// A special case for falsey return values
class FalseyValue {
	public function __construct( $value ) {
		$this->value = $value;
	}

	public function get_value() {
		return $this->value;
	}
}
