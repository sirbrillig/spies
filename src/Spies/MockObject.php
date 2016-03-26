<?php
namespace Spies;

class MockObject {

	private $class_name = null;

	public function __construct( $class_name = null ) {
		$this->class_name = $class_name;
		if ( isset( $class_name ) ) {
			array_map( [ $this, 'add_method' ], get_class_methods( $class_name ) );
		}
	}

	public function __call( $function_name, $args ) {
		if ( ! isset( $this->$function_name ) || ! is_callable( $this->$function_name ) ) {
			throw new \Exception( 'Attempted to call un-mocked method "' . $function_name . '" with ' . json_encode( $args ) );
		}
		return call_user_func_array( $this->$function_name, $args );
	}

	public function spy_on_method( $function_name, $function = null ) {
		return $this->add_method( $function_name, $function );
	}

	public function add_method( $function_name, $function = null ) {
		if ( ! isset( $function ) ) {
			$function = new \Spies\Spy();
		}
		if ( ! is_callable( $function ) ) {
			throw new \Exception( 'The function "' . $function_name . '" added to this mock object was not a function' );
		}
		if ( $function instanceof Spy ) {
			$function->set_function_name( $function_name );
		}
		$this->$function_name = $function;
		return $function;
	}

	public static function mock_object( $class_name = null) {
		return new MockObject( $class_name );
	}

	public static function mock_object_of( $class_name = null) {
		return new MockObject( $class_name );
	}
}
