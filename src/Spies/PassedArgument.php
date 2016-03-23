<?php
namespace Spies;

class PassedArgument {
	public $index = 0;
	public function __construct( $index ) {
		$this->index = $index;
	}
}
