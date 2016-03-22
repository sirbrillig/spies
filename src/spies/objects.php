<?php
namespace Spies;

class MockObject {

	public function __call( $function_name, $args ) {
		if ( ! isset( $this->$function_name ) || ! is_callable( $this->$function_name ) ) {
			throw new \Exception( 'Attempted to call un-mocked method "' . $function_name . '" with ' . json_encode( $args ) );
		}
		return call_user_func_array( $this->$function_name, $args );
	}

	public function add_method( $function_name, $function = null ) {
		if ( ! isset( $function ) ) {
			$function = new \Spies\Spy();
		}
		if ( ! is_callable( $function ) ) {
			throw new \Exception( 'The function "' . $function_name . '" added to this mock object was not a function' );
		}
		if ( $function instanceof Spy ) {
			$function->function_name = $function_name;
		}
		$this->$function_name = $function;
		return $function;
	}

	public static function mock_object() {
		return new MockObject();
	}
}
