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

	/**
 	 * Alias for add_method
	 *
	 * @param string $function_name The name of the function to add to this object
	 * @param Spy|callback $function optional A callable function or Spy to be used when the new method is called. Defaults to a new Spy.
	 * @return Spy|callback The Spy or callback
	 */
	public function spy_on_method( $function_name, $function = null ) {
		return $this->add_method( $function_name, $function );
	}

	/**
	 * Add a public method to this object and return that method.
	 *
	 * Creates and returns a Spy if no function is provided.
	 *
	 * When called without a second argument, returns a stub (which, remember, is
	 * also a Spy), so you can program its behavior or query it for expectations.
	 * You can also use the second argument to pass a function (or Spy)
	 * explicitly, in which case whatever you pass is what will be returned.
	 *
	 * Since it returns a Spy by default, you can use this to stub behaviors:
	 *
	 * `$mock_object->add_method( 'do_something' )->that_returns( 'hello world' );`
	 *
	 * @param string $function_name The name of the function to add to this object
	 * @param Spy|callback $function optional A callable function or Spy to be used when the new method is called. Defaults to a new Spy.
	 * @return Spy|callback The Spy or callback
	 */
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
