<?php
namespace Spies;

abstract class TestCase extends \PHPUnit\Framework\TestCase {

	public static function wasCalled() {
		return new \Spies\SpiesConstraintWasCalled;
	}

	public static function wasNotCalled() {
		return new \Spies\SpiesConstraintWasNotCalled;
	}

	public static function wasCalledWith( $args ) {
		return new \Spies\SpiesConstraintWasCalledWith( $args );
	}

	public static function wasCalledTimes( $count ) {
		return new \Spies\SpiesConstraintWasCalledTimes( $count );
	}

	public static function wasCalledBefore( $spy ) {
		return new \Spies\SpiesConstraintWasCalledBefore( $spy );
	}

	public static function wasCalledWhen( $callable ) {
		return new \Spies\SpiesConstraintWasCalledWhen( $callable );
	}

	public static function wasCalledTimesWith( $count, $args ) {
		return new \Spies\SpiesConstraintWasCalledTimesWith( $count, $args );
	}

	public static function assertSpyWasCalled( $condition, $message = '' ) {
		self::assertThat( $condition, self::wasCalled(), $message );
	}

	public static function assertSpyWasNotCalled( $condition, $message = '' ) {
		self::assertThat( $condition, self::wasNotCalled(), $message );
	}

	public static function assertSpyWasNotCalledWith( $condition, $args, $message = '' ) {
		self::assertThat( $condition, self::logicalNot( self::wasCalledWith( $args ) ), $message );
	}

	public static function assertSpyWasCalledWith( $condition, $args, $message = '' ) {
		self::assertThat( $condition, self::wasCalledWith( $args ), $message );
	}

	public static function assertSpyWasCalledTimes( $condition, $count, $message = '' ) {
		self::assertThat( $condition, self::wasCalledTimes( $count ), $message );
	}

	public static function assertSpyWasNotCalledTimes( $condition, $count, $message = '' ) {
		self::assertThat( $condition, self::logicalNot( self::wasCalledTimes( $count ) ), $message );
	}

	public static function assertSpyWasCalledBefore( $condition, $target_spy, $message = '' ) {
		self::assertThat( $condition, self::wasCalledBefore( $target_spy ), $message );
	}

	public static function assertSpyWasNotCalledBefore( $condition, $target_spy, $message = '' ) {
		self::assertThat( $condition, self::logicalNot( self::wasCalledBefore( $target_spy ) ), $message );
	}

	public static function assertSpyWasCalledWhen( $condition, $callable, $message = '' ) {
		self::assertThat( $condition, self::wasCalledWhen( $callable ), $message );
	}

	public static function assertSpyWasNotCalledWhen( $condition, $callable, $message = '' ) {
		self::assertThat( $condition, self::logicalNot( self::wasCalledWhen( $callable ) ), $message );
	}

	public static function assertSpyWasCalledTimesWith( $condition, $count, $args, $message = '' ) {
		self::assertThat( $condition, self::wasCalledTimesWith( $count, $args ), $message );
	}

	public static function assertSpyWasNotCalledTimesWith( $condition, $count, $args, $message = '' ) {
		self::assertThat( $condition, self::logicalNot( self::wasCalledTimesWith( $count, $args ) ), $message );
	}

}
