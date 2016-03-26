<?php
namespace Spies;

class SpyCall {
	private $args;
	private $timestamp;

	public function __construct( $args ) {
		$this->timestamp = microtime();
		$this->args = $args;
	}

	/**
 	 * Get the arguments passed to this call
	 *
	 * @return array The argument list
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Get the timestamp when this call happened
	 *
	 * @return string the timestamp
	 */
	public function get_timestamp() {
		return $this->timestamp;
	}
}
