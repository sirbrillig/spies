<?php
namespace Spies;

class SpyCallFormatter {
	private $calls;

	public function __construct( $calls ) {
		$this->calls = $calls;
	}

	public function __toString() {
		$i = 0;
		return "\n " . implode( ",\n ", array_map( function( $call ) use ( &$i ) {
			$i++;
			return $i . '. ' . strval( $call );
		}, $this->calls ) ) . "\n";
	}
}
