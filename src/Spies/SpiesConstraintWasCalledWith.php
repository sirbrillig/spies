<?php
namespace Spies;

class SpiesConstraintWasCalledWith extends BaseConstraint {
	private $expected_args;
	private $negation;

	public function __construct( $args, $negation = false ) {
		parent::__construct();
		$this->expected_args = $args;
		$this->negation = $negation;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		if ( $this->negation ) {
			return ! $other->was_called_with_array( $this->expected_args );
		}
		return $other->was_called_with_array( $this->expected_args );
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$this->negation ?$generator->spy_was_called_with( $other, $this->expected_args ) : $generator->spy_was_not_called_with( $other, $this->expected_args );
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

