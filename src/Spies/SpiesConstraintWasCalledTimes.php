<?php
namespace Spies;

class SpiesConstraintWasCalledTimes extends BaseConstraint {
	private $count;
	private $negation;

	public function __construct( $count, $negation = false ) {
		parent::__construct();
		$this->count = $count;
		$this->negation = $negation;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		if ( $this->negation ) {
			return ! $other->was_called_times( $this->count );
		}
		return $other->was_called_times( $this->count );
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$this->negation ? $generator->spy_was_called_times( $other, $this->count ) : $generator->spy_was_not_called_times( $other, $this->count );
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

