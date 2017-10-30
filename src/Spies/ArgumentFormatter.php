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
		$stringify_argument = function ( $argument ) {
			if ( method_exists( $argument, '__toString' ) ) {
				return $argument->__toString();
			}
			return json_encode( $argument );
		};
		return implode( ', ', array_map( $stringify_argument, $this->args ) );
	}
}
