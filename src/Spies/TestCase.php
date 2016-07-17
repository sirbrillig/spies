<?php
namespace Spies;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
	public static function assertSpyWasCalled( $condition, $message = '' ) {
		self::assertThat( $condition, self::wasCalled(), $message );
	}

	public static function assertSpyWasNotCalled( $condition, $message = '' ) {
		self::assertThat( $condition, self::wasNotCalled(), $message );
	}

	public static function wasCalled() {
		return new \Spies\SpiesConstraintWasCalled;
	}

	public static function wasNotCalled() {
		return new \Spies\SpiesConstraintWasNotCalled;
	}

	public static function wasCalledWith( $args ) {
		return new \Spies\SpiesConstraintWasCalledWith( $args );
	}

	public static function assertSpyWasCalledWith( $condition, $args, $message = '' ) {
		self::assertThat( $condition, self::wasCalledWith( $args ), $message );
	}

}
