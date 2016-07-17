<?php
namespace Spies;

class FailureGenerator {
	private $messages = [];

	public function spy_was_not_called( $spy ) {
		$this->add_message( $spy->get_function_name() . ' is called' );
	}

	public function spy_was_called( $spy ) {
		$this->add_message( $spy->get_function_name() . ' is not called' );
	}

	public function spy_was_not_called_with( $spy, $args ) {
		$this->spy_was_not_called( $spy );
		$desc = 'with ';
		$desc .= strval( new ArgumentFormatter( $args ) );
		$this->add_message( $desc );
	}

	public function spy_was_not_called_with_additional( $spy ) {
		$desc = $spy->get_function_name() . ' was actually ';
		$calls = $spy->get_called_functions();
		$desc .= empty( $calls ) ? 'not called at all' : 'called with:' . strval( new SpyCallFormatter( $calls ) );
		$this->add_message( $desc );
	}

	public function add_message( $message ) {
		$this->messages[] = $message;
	}

	public function get_message() {
		return implode( ' ', $this->messages );
	}
}
