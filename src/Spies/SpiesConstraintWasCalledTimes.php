<?php
namespace Spies;

class SpiesConstraintWasCalledTimes extends \PHPUnit_Framework_Constraint {
	private $count;

	public function __construct( $count ) {
		parent::__construct();
		$this->count = $count;
	}

	public function matches( $other ) {
		if ( ! $other instanceof \Spies\Spy ) {
			return false;
		}
		return $other->was_called_times( $this->count );
	}

	public function failureDescription( $other ) {
		$generator = new FailureGenerator();
		$generator->spy_was_not_called_times( $other, $this->count );
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

