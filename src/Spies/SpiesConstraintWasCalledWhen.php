<?php
namespace Spies;

class SpiesConstraintWasCalledWhen extends BaseConstraint {
	private $expected_callable;
	private $negation;

	public function __construct( $callable, $negation ) {
		parent::__construct();
		$this->expected_callable = $callable;
		$this->negation = $negation;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		$result = $other->was_called_when( $this->expected_callable );
		return $this->negation ? ( ! $result ) : $result;
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$this->negation ? $generator->spy_was_called_when( $other ) : $generator->spy_was_not_called_when( $other );
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

