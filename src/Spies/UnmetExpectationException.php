<?php
namespace Spies;

if ( class_exists( '\PHPUnit_Framework_ExpectationFailedException' ) ) {
	class UnmetExpectationException extends \PHPUnit_Framework_ExpectationFailedException {
	}
} else {
	class UnmetExpectationException extends \Exception {
	}
}
