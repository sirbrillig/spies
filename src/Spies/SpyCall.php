<?php
namespace Spies;

class SpyCall {
	private $args;
	private $timestamp;

	public function __construct( $args ) {
		$this->timestamp = microtime();
		$this->args = $args;
	}

	public function get_args() {
		return $this->args;
	}

	public function get_timestamp() {
		return $this->timestamp;
	}
}
