<?php
namespace Spies;

class SpiesConstraintWasCalled extends \PHPUnit_Framework_Constraint {
	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return $other->was_called();
	}

	public function toString() {
		return 'was called';
	}
}
