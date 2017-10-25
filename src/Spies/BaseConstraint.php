<?php

namespace Spies;

abstract class BaseConstraint {
	public function __construct() {
	}

	public function failureDescription( $other ) {
		return '';
	}

	public function additionalFailureDescription( $other ) {
		return '';
	}
}
