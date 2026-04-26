<?php
namespace Spies\PHPUnit;

class ConstraintWrapper extends \PHPUnit\Framework\Constraint\Constraint {
	private $wrapped_constraint;

	public function __construct( $constraint ) {
		$this->wrapped_constraint = $constraint;
	}

	public function matches( $other ): bool {
		return $this->wrapped_constraint->matches( $other );
	}

	public function failureDescription( $other ): string {
		return $this->wrapped_constraint->failureDescription( $other );
	}

	public function additionalFailureDescription( $other ): string {
		return $this->wrapped_constraint->additionalFailureDescription( $other );
	}

	public function toString(): string {
		return $this->wrapped_constraint->toString();
	}
}


