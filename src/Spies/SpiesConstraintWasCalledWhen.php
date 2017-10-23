<?php
namespace Spies;

class SpiesConstraintWasCalledWhen extends BaseConstraint {
	private $expected_callable;

	public function __construct( $callable ) {
		parent::__construct();
		$this->expected_callable = $callable;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return $other->was_called_when( $this->expected_callable );
	}

	protected function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called_when( $other );
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

