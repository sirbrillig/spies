<?php
namespace Spies;

class SpiesConstraintWasCalledTimesWith extends BaseConstraint {
	private $expected_args;
	private $count;
	private $negation;

	public function __construct( $count, $args, $negation = false ) {
		parent::__construct();
		$this->expected_args = $args;
		$this->count = $count;
		$this->negation = $negation;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		$result = $other->was_called_times_with_array( $this->count, $this->expected_args );
		return $this->negation ? ( ! $result ) : $result;
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$this->negation ? $generator->spy_was_called_with_times( $other, $this->expected_args, $this->count ) : $generator->spy_was_not_called_with_times( $other, $this->expected_args, $this->count );
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


