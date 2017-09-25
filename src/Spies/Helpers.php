<?php
namespace Spies;

class Helpers {
	/**
	 * Compare argument lists
	 *
	 * You should not need to call this directly.
	 */
	public static function do_args_match( $a, $b ) {
		if ( $a === $b ) {
			return true;
		}
		if ( count( $a ) !== count( $b ) ) {
			return false;
		}
		$index = 0;
		foreach ( $a as $arg ) {
			if ( ! self::do_vals_match( $arg, $b[ $index ] ) ) {
				return false;
			}
			$index ++;
		}
		return true;
	}

	private static function do_vals_match( $a, $b ) {
		if ( $a === $b ) {
			return true;
		}
		if ( $a instanceof \Spies\AnyValue || $b instanceof \Spies\AnyValue ) {
			return true;
		}
		if ( $a instanceof \Spies\MatchPattern && $a->is_match( $b ) ) {
			return true;
		}
		if ( $b instanceof \Spies\MatchPattern && $b->is_match( $a ) ) {
			return true;
		}
		if ( $a instanceof \Spies\MatchArray && $a->is_match( $b ) ) {
			return true;
		}
		if ( $b instanceof \Spies\MatchArray && $b->is_match( $a ) ) {
			return true;
		}
		return false;
	}

	public static function do_arrays_match( $a, $b ) {
		return self::do_vals_match( $a, $b );
	}
}
