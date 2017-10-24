<?php
namespace Spies;

class SpiesConstraintWasCalled extends BaseConstraint {
	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return $other->was_called();
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called( $other );
		return $generator->get_message();
	}

	protected function additionalFailureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called_with_additional( $other );
		return $generator->get_message();
	}

	public function toString() {
		return '';
	}
}
