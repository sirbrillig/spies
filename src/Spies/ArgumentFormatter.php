<?php
namespace Spies;

class ArgumentFormatter {
	private $args;

	public function __construct( $args ) {
		$this->args = $args;
	}

	public function __toString() {
		if ( empty( $this->args ) ) {
			return 'no arguments';
		}
		return 'arguments: ( ' . $this->get_args_as_array() . ' )';
	}

	private function get_args_as_array() {
		return implode( ', ', array_map( 'json_encode', $this->args ) );
	}
}
