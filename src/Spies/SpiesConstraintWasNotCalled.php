<?php
namespace Spies;

class SpiesConstraintWasNotCalled extends \PHPUnit_Framework_Constraint {
	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return ! $other->was_called();
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_called( $other );
		return $generator->get_message();
	}

	public function toString() {
		return '';
	}
}

