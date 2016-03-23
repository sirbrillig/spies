<?php

require_once __DIR__ . '/../vendor/autoload.php';

class SpyTest extends PHPUnit_Framework_TestCase {
	public function test_spy_was_called() {
		$spy = new \Spies\Spy();
		$spy();
		$this->assertTrue( $spy->was_called() );
	}

	public function test_make_spy() {
		$spy = \Spies\make_spy();
		$spy();
		$this->assertTrue( $spy->was_called() );
	}
}
