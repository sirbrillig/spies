<?php
namespace Spies\PHPUnit;

class ConstraintWrapper extends \PHPUnit_Framework_Constraint {
	public function __construct( $constraint ) {
		parent::__construct();
		$this->wrapped_constraint = $constraint;
	}

	public function matches( $other ) {
		return $this->wrapped_constraint->matches( $other );
	}

	public function failureDescription( $other ) {
		return $this->wrapped_constraint->failureDescription( $other );
	}

	public function additionalFailureDescription( $other ) {
		return $this->wrapped_constraint->additionalFailureDescription( $other );
	}

	public function toString() {
		return $this->wrapped_constraint->toString();
	}
}


