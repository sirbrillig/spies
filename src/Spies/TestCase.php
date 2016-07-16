<?php
namespace Spies;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
	public static function assertSpyWasCalled( $condition, $message = '' ) {
		self::assertThat( $condition, self::wasCalled(), $message );
	}

	public static function assertSpyWasNotCalled( $condition, $message = '' ) {
		self::assertThat( $condition, self::logicalNot( self::wasCalled() ), $message );
	}

	public static function wasCalled() {
		return new \Spies\SpiesConstraintWasCalled;
	}
}
