<?php
namespace Spies;

class SpiesConstraintWasCalledBefore extends BaseConstraint {
	private $target_spy;
	private $negation;

	public function __construct( $target_spy, $negation ) {
		parent::__construct();
		$this->target_spy = $target_spy;
		$this->negation = $negation;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		$result = $other->was_called_before( $this->target_spy );
		return $this->negation ? ( ! $result ) : $result;
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$this->negation ? $generator->spy_was_called_before( $other, $this->target_spy ) : $generator->spy_was_not_called_before( $other, $this->target_spy );
		return $generator->get_message();
	}

	public function toString() {
		return '';
	}
}


