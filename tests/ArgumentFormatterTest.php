<?php

class ArgumentFormatterTest extends PHPUnit_Framework_TestCase {
	public function tearDown() {
		\Spies\finish_spying();
	}

	public function test__expect_to_string_returns_no_arguments_when_no_arguments_provided() {
		$formatter = new \Spies\ArgumentFormatter( [] );
		$this->assertEquals( 'no arguments', (string) $formatter );
	}

	public function test__expect_to_string_calls_to_string_from_arg_when_available() {
		$argument = $this->getMockBuilder( 'stdClass' )
			->setMethods( [ '__toString' ] )
			->getMock();

		$argument->expects( $this->once() )
			->method( '__toString' )
			->willReturn( 'stringified argument' );

		$formatter = new \Spies\ArgumentFormatter( [ $argument ] );
		$this->assertEquals( 'arguments: ( stringified argument )', (string) $formatter );
	}

	public function test__expect_to_string_returns_stringified_array_when_arg_is_array() {
		$formatter = new \Spies\ArgumentFormatter( [ [ 'foo' => 'bar' ] ] );
		$this->assertEquals( 'arguments: ( {"foo":"bar"} )', (string) $formatter );
	}

	public function test__expect_to_string_returns_string_when_arg_is_string() {
		$formatter = new \Spies\ArgumentFormatter( [ 'foo' ] );
		$this->assertEquals( 'arguments: ( "foo" )', (string) $formatter );
	}
}
