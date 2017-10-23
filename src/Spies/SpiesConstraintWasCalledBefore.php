<?php
namespace Spies;

class SpiesConstraintWasCalledBefore extends BaseConstraint {
	private $target_spy;

	public function __construct( $target_spy ) {
		parent::__construct();
		$this->target_spy = $target_spy;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return $other->was_called_before( $this->target_spy );
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called_before( $other, $this->target_spy );
		return $generator->get_message();
	}

	public function toString() {
		return '';
	}
}


