<?php
namespace Spies;

class SpiesConstraintWasCalledTimesWith extends BaseConstraint {
	private $expected_args;
	private $count;

	public function __construct( $count, $args ) {
		parent::__construct();
		$this->expected_args = $args;
		$this->count = $count;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return $other->was_called_times_with_array( $this->count, $this->expected_args );
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called_with( $other, $this->expected_args );
		return $generator->get_message();
	}

	public function additionalFailureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called_with_additional( $other );
		return $generator->get_message();
	}

	public function toString() {
		return '';
	}
}


