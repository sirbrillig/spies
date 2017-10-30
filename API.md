# Spies API Reference

## functions

### `make_spy()`

Shortcut for `new Spy()`.

```php
$spy = make_spy();
$spy();
```

### `get_spy_for( $function_name )`

Spy on a global or namespaced function. Shortcut for `Spy::stub_function( $function_name )`.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post();
```

### `stub_function( $function_name )`

Stub a global or namespaced function. Shortcut for `Spy::stub_function( $function_name )`.

```php
stub_function( 'wp_update_post' );
wp_update_post();
```

### `mock_function( $function_name )`

Alias for `stub_function()`.


```php
mock_function( 'wp_update_post' );
wp_update_post();
```

### `expect_spy( $spy )`

Shortcut for `Expectation::expect_spy( $spy )`.


```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post();
$expectation = expect_spy( $spy )->to_have_been_called();
$expectation->verify();
```

### `mock_object()`

Shortcut for `MockObject::mock_object()`. Can also be used to create a
mock object with a delegate.

```php
$obj = mock_object();
$obj->add_method( 'run' );
$obj->run();
```

```php
$mock = \Spies\mock_object( new Greeter() );
$say_goodbye = $mock->spy_on_method( 'say_goodbye' );
$mock->add_method( 'say_hello' )->that_returns( 'greetings' );
$this->assertEquals( 'greetings', $mock->say_hello() );
$this->assertEquals( 'goodbye', $mock->say_goodbye() );
$this->assertSpyWasCalled( $say_goodbye );
```

### `mock_object_of( $class_name )`

Mock an instance of an existing class with all its methods. Shortcut for `MockObject::mock_object( $class_name )`.

```php
class TestObj {
  public function run() {
  }
}
$obj = mock_object_of( 'TestObj' );
$obj->run();
```

### `finish_spying()`

Resolve all global Expectations, then clear all Expectations and all global Spies. Shortcut for `GlobalExpectations::resolve_delayed_expectations()`, `GlobalExpectations::clear_all_expectations()`, and `GlobalSpies::clear_all_spies`.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post();
expect_spy( $spy )->to_have_been_called();
finish_spying();
```

### `any()`

Used as an argument to `Expectation->with()` to mean "any argument". Shortcut for `new AnyValue()`.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post( [ 'title' => 'hello' ] );
expect_spy( $spy )->to_have_been_called->with( any() );
finish_spying();
```

### `match_pattern()`

Used as an argument to `Expectation->with()` or `Spy()->with()` to mean "any string argument matching this PCRE pattern". Shortcut for `new MatchPattern()`.

```php
$spy = get_spy_for( 'run_experiment' );
run_experiment( 'slartibartfast' );
expect_spy( $spy )->to_have_been_called->with( match_pattern( '/bart/' ) );
finish_spying();
```

```php
mock_function( 'slartibartfast' )->when_called->with( match_pattern( '/bart/' ) )->will_return( 14 );
$id = run_experiment( 'slartibartfast' );
$this->assertEquals( 14, $id );
```

### `match_array()`

Used as an argument to `Expectation->with()` or `Spy()->with()` to mean "any argument with these values". Shortcut for `new MatchArray()`.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post( [ 'title' => 'hello', 'status' => 'publish', 'id' => 14, 'post_content' => 'slartibartfast' ] );
expect_spy( $spy )->to_have_been_called->with( match_array( [ 'status' => 'publish', 'post_content' => 'slartibartfast' ] ) );
finish_spying();
```

```php
mock_function( 'wp_update_post' )->when_called->with( match_array( [ 'title' => 'hello' ] ) )->will_return( 14 );
$id = wp_update_post( [ 'title' => 'hello', 'status' => 'publish', 'post_content' => 'slartibartfast' ] );
$this->assertEquals( 14, $id );
```

### `passed_arg( $index )`

Used as an argument to `Spy->and_return()` to mean "return the passed argument at $index". Shortcut for `new PassedArgument( $index )`.

```php
stub_function( 'wp_update_post' )->and_return( passed_arg( 1 ) );
$value = wp_update_post( 'hello' );
$this->assertEquals( 'hello', $value );
```

### `do_arrays_match( $a, $b )`

Compare two arrays allowing usage of `match_array()`.

```php
$array = [ 'baz' => 'boo', 'foo' => 'bar' ];
$this->assertTrue( \Spies\do_arrays_match( $array, \Spies\match_array( [ 'foo' => 'bar' ] ) ) );
```

## Spy

### Static methods

### `get_spy_for( $function_name )`

Create a new global or namespaced function and attach it to a new Spy, returning that Spy.

```php
$spy = Spy::get_spy_for( 'wp_update_post' );
wp_update_post();
expect_spy( $spy )->to_have_been_called();
finish_spying();
```

### Instance methods

### `get_function_name()`

Return the spy's function name. Really only useful when spying on global or namespaced functions. Defaults to "a spy".

```php
$spy = get_spy_for( 'wp_update_post' );
$this->assertEquals( 'wp_update_post', $spy->get_function_name() );
$spy2 = make_spy();
$this->assertEquals( 'a spy', $spy2->get_function_name() );
```

### `set_function_name()`

Set the spy's function name. You generally don't need to use this.

```php
$spy = make_spy();
$spy->set_function_name( 'foo' );
$this->assertEquals( 'foo', $spy->get_function_name() );
```

### `call( $arg... )`

Call the Spy. It's probably easier to just call the Spy as a function like this: `$spy()`.

```php
$spy = make_spy();
$spy->call( 1, 2, 3 );
$this->assertSpyWasCalledWith( $spy, [ 1, 2, 3 ] );
```

### `call_with_array( $args )`

Call the Spy with an array of arguments. It's probably easier to just call the Spy as a function.

```php
$spy = make_spy();
$spy->call_with_array( [ 1, 2, 3 ] );
$this->assertSpyWasCalledWith( $spy, [ 1, 2, 3 ] );
```

### `clear_call_record()`

Clear the Spy's call record. You shouldn't need to call this.

```php
$spy = make_spy();
$spy();
$spy->clear_call_record();
$this->assertSpyWasNotCalled( $spy );
```

### `get_called_functions()`

Get the raw call record for the Spy. Each call is an instance of `SpyCall`.

```php
$spy = make_spy();
$spy->call_with_array( [ 1, 2, 3 ] );
$calls = $spy->get_called_functions();
$this->assertEquals( [ 1, 2, 3 ], $calls[0]->get_args() );
```

### `was_called()`

Return true if the Spy was called.

```php
$spy = make_spy();
$spy();
$this->assertTrue( $spy->was_called() );
```

### `was_called_with( $arg... )`

Return true if the Spy was called with specific arguments.

```php
$spy = make_spy();
$spy( 'a', 'b' );
$this->assertTrue( $spy->was_called_with( 'a', 'b' ) );
```

### `was_called_when( $callable )`

Return true if the passed function returns true at least once. For each spy call, the function will be called with the arguments from that call as an array.

```php
$spy = make_spy();
$spy( 'a' );
$this->assertTrue( $spy->was_called_when( function( $args ) {
  return ( $args[0] === 'a' );
} ) );
```

### `was_called_times( $count )`

Return true if the Spy was called exactly `$count` times.

```php
$spy = make_spy();
$spy();
$spy();
$this->assertTrue( $spy->was_called_times( 2 ) );
```

### `was_called_times_with( $count, $arg... )`

Return true if the Spy was called exactly $count times with specific arguments.

```php
$spy = make_spy();
$spy( 'a', 'b' );
$spy( 'a', 'b' );
$spy( 'c', 'd' );
$this->assertTrue( $spy->was_called_times_with( 2, 'a', 'b' ) );
```

### `was_called_before( $spy )`

Return true if the Spy was called before $spy.

```php
$spy = make_spy();
$spy2 = make_spy();
$spy();
$spy2();
$this->assertTrue( $spy->was_called_before( $spy2 ) );
```

### `get_times_called()`

Return the number of times the Spy was called.

```php
$spy = make_spy();
$spy();
$spy();
$this->assertEquals( 2, $spy->get_times_called() );
```

### `get_call( $index )`

Return the call record for a single call.

```php
$spy = make_spy();
$spy( 'a' );
$spy( 'b' );
$call = $spy->get_call( 0 );
$this->assertEquals( [ 'a' ], $call->get_args() );
```

## Stub (Stubs are actually just instances of Spy used differently)

### Static methods

### `stub_function( $function_name )`

Create a new global or namespaced function and attach it to a new Spy, returning that Spy.

```php
Spy::stub_function( 'say_hello' )->and_return( 'hello' );
$this->assertEquals( 'hello', say_hello() );
```

### Instance methods

### `and_return( $value )`

Instruct the stub to return $value when called. $value can also be a function to call when the stub is called.

```php
Spy::stub_function( 'say_hello' )->and_return( 'hello' );
$this->assertEquals( 'hello', say_hello() );
```

### `will_return( $value )`

Alias for `and_return( $value )`.

```php
Spy::stub_function( 'say_hello' )->when_called->will_return( 'hello' );
$this->assertEquals( 'hello', say_hello() );
```

### `that_returns( $value )`

Alias for `and_return( $value )`.

```php
$obj = mock_object();
$obj->add_method( 'run' )->that_returns( 'hello' );
$this->assertEquals( 'hello', $obj->say_hello() );
```

### `with( $arg... )`

Changes behavior of next `and_return()` to be a conditional return value.

```php
Spy::stub_function( 'say_hello' )->when_called->will_return( 'beep' );
Spy::stub_function( 'say_hello' )->when_called->with( 'human' )->will_return( 'hello' );
$this->assertEquals( 'hello', say_hello( 'human' ) );
$this->assertEquals( 'beep', say_hello( 'robot' ) );
```

### `when_called`

Syntactic sugar. Returns the Stub.

```php
Spy::stub_function( 'say_hello' )->when_called->will_return( 'hello' );
$this->assertEquals( 'hello', say_hello() );
```

### `and_return_first_argument()`

Shortcut for `and_return( passed_arg( 0 ) )`.

```php
Spy::stub_function( 'say_hello' )->and_return_first_argument();
$this->assertEquals( 'hi', say_hello( 'hi' ) );
```

### `and_return_second_argument()`

Shortcut for `and_return( passed_arg( 1 ) )`.

```php
Spy::stub_function( 'say_hello' )->and_return_second_argument();
$this->assertEquals( 'there', say_hello( 'hi', 'there' ) );
```

## SpyCall

## Instance methods

### `get_args()`

Return the arguments for a call.

```php
$spy = make_spy();
$spy->call_with_array( [ 1, 2, 3 ] );
$calls = $spy->get_called_functions();
$this->assertEquals( [ 1, 2, 3 ], $calls[0]->get_args() );
```

### `get_timestamp()`

Return the timestamp for when a call was made.

```php
$spy = make_spy();
$now = microtime();
$spy->call_with_array( [ 1, 2, 3 ] );
$calls = $spy->get_called_functions();
$this->assertGreaterThan( $now, $calls[0]->get_timestamp() );
```

## MockObject

### Static methods

### `mock_object()`

Shortcut for `new MockObject()`. If a class instance is passed as an
argument, it creates a delegate instance, forwarding all method calls on
the MockObject to the delegate instance.

```php
$obj = Spies\MockObject::mock_object();
$obj->add_method( 'run' );
$obj->run();
```

Using a delegate:

```php
class Greeter {
	public function say_hello() {
		return 'hello';
	}

	public function say_goodbye() {
		return 'goodbye';
	}
}

function test_greeter() {
	$mock = Spies\MockObject::mock_object( new Greeter() );
	$say_goodbye = $mock->spy_on_method( 'say_goodbye' );
	$mock->add_method( 'say_hello' )->that_returns( 'greetings' );
	$this->assertEquals( 'greetings', $mock->say_hello() );
	$this->assertEquals( 'goodbye', $mock->say_goodbye() );
	$this->assertSpyWasCalled( $say_goodbye );
}
```

### `mock_object_of( $class_name )`

Create a new `MockObject`, automatically adding a Spy for every public method in `$class_name`.

```php
class TestObj {
  public function run() {
  }
}
$obj = Spies\MockObject::mock_object_of( 'TestObj' );
$obj->run();
```

### Instance methods

### `add_method( $function_name, $function = null )`

Add a public method to this Object as a Spy and return that method. Creates and returns a Spy if no function is provided.

```php
$obj = Spies\MockObject::mock_object();
$obj->add_method( 'run', function( $arg ) {
	return 'hello ' . $arg;
} );
$this->assertEquals( 'hello friend', $obj->run( 'friend' ) );
```

### `spy_on_method( $function_name, $function = null )`

Alias for `add_method()`.

```php
$obj = Spies\MockObject::mock_object();
$spy = $obj->get_spy_for( 'run' );
$obj->run();
expect_spy( $spy )->to_have_been_called();
finish_spying();
```

### `and_ignore_missing()`

Prevents throwing an Exception when an unmocked method is called on this object.

```php
$mock = Spies\mock_object()->and_ignore_missing();
$this->assertEquals( null, $mock->say_goodbye() );
```

## Expectation

### Static methods

### `expect_spy( $spy )`

Create a new Expectation for the behavior of $spy.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post();
$expectation = expect_spy( $spy )->to_have_been_called();
$expectation->verify();
```

### Instance methods

### `to_be_called`

Syntactic sugar. Returns the Expectation.

```php
$spy = get_spy_for( 'wp_update_post' );
$expectation = expect_spy( $spy )->to_be_called();
wp_update_post();
$expectation->verify();
```

### `to_have_been_called`

Syntactic sugar. Returns the Expectation.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post();
$expectation = expect_spy( $spy )->to_have_been_called();
$expectation->verify();
```

### `not`

When accessed, reverses all expected behaviors on this Expectation.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post( 'bye' );
$expectation = expect_spy( $spy )->not->to_have_been_called->with( 'hello' );
$expectation->verify();
```

### `verify()`

Resolve and verify all the behaviors set on this Expectation.

If any behavior is false, this will throw an `\Spies\UnmetExpectationException`. If PHPUnit is loaded the Exception will be a subclass of `PHPUnit_Framework_ExpectationFailedException`.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post( 'bye' );
$expectation = expect_spy( $spy )->not->to_have_been_called->with( 'hello' );
$expectation->verify();
```

### `met_expectations()`

Returns true if all behaviors in this Expectation are met.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post( 'bye' );
$expectation = expect_spy( $spy )->not->to_have_been_called->with( 'hello' );
$this->assertTrue( $expectation->met_expectations() );
```

### `get_fail_message()`

Returns the first failure message for the behaviors on this Expectation.

Returns null if no behaviors failed.

```php
$spy = make_spy();
$expectation = expect_spy( $spy )->to_have_been_called();
$this->assertContains( 'Failed asserting that a spy is called', $expectation->get_fail_message() );
```

### `to_be_called()`

Add an expected behavior that the spy was called when this is resolved.

```php
$spy = get_spy_for( 'wp_update_post' );
$expectation = expect_spy( $spy )->to_be_called();
wp_update_post();
$expectation->verify();
```

### `to_have_been_called()`

Alias for `to_be_called()`.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post();
$expectation = expect_spy( $spy )->to_have_been_called();
$expectation->verify();
```

### `with( $arg... )`

Add an expected behavior that the spy was called with particular arguments when this is resolved.

```php
$spy = get_spy_for( 'wp_update_post' );
wp_update_post( 'hello' );
$expectation = expect_spy( $spy )->to_have_been_called->with( 'hello' );
$expectation->verify();
```

### `when( $callable )`

Return true if the passed function returns true at least once. For each spy call, the function will be called with the arguments from that call.

```php
$spy = make_spy();
$spy( 'a' );
expect_spy( $spy )->to_have_been_called->when( function( $args ) {
  return ( $args[0] === 'a' );
} ) );
finish_spying();
```

### `times( $count )`

Add an expected behavior that the spy was called exactly $count times.

```php
$spy = make_spy();
$spy( 'a' );
$spy( 'b' );
expect_spy( $spy )->to_have_been_called->times( 2 );
finish_spying();
```

### `once()`

Alias for `times( 1 )`.

```php
$spy = make_spy();
$spy( 'a' );
expect_spy( $spy )->to_have_been_called->once();
finish_spying();
```

### `twice()`

Alias for `times( 2 )`.

```php
$spy = make_spy();
$spy( 'a' );
$spy( 'b' );
expect_spy( $spy )->to_have_been_called->twice();
finish_spying();
```

### `before( $spy )`

Add an expected behavior that the spy was called before $spy.

```php
$spy = make_spy();
$spy2 = make_spy();
$spy();
$spy2();
expect_spy( $spy )->to_have_been_called->before( $spy2 );
finish_spying();
```

## PHPUnit Custom Assertions

These are methods available on instances of `\Spies\TestCase`, which is loaded only if PHPUnit is loaded.

### Constraints for `assertThat()`

### `wasCalled()`
### `wasNotCalled()`
### `wasCalledTimes( $count )`
### `wasCalledBefore( $spy )`
### `wasCalledWhen( $callable )`

### Assertions

### `assertSpyWasCalled( $spy )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello();
$this->assertSpyWasCalled( $spy );
```

### `assertSpyWasNotCalled( $spy )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
$this->assertSpyWasNotCalled( $spy );
```

### `assertSpyWasCalledWith( $spy, $args )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'friend' );
$this->assertSpyWasCalledWith( $spy, [ 'friend' ] );
```

### `assertSpyWasNotCalledWith( $spy, $args )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'robot' );
$this->assertSpyWasNotCalledWith( $spy, [ 'friend' ] );
```

### `assertSpyWasCalledTimes( $spy, $count )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'friend' );
say_hello( 'robot' );
$this->assertSpyWasCalledTimes( $spy, 2 );
```

### `assertSpyWasNotCalledTimes( $spy, $count )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'friend' );
say_hello( 'robot' );
$this->assertSpyWasNotCalledTimes( $spy, 3 );
```

### `assertSpyWasCalledTimesWith( $spy, $count, $args )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'friend' );
say_hello( 'friend' );
$this->assertSpyWasCalledTimesWith( $spy, 2, [ 'friend' ] );
```

### `assertSpyWasNotCalledTimesWith( $spy, $count, $args )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'friend' );
say_hello( 'robot' );
$this->assertSpyWasNotCalledTimesWith( $spy, 2, [ 'friend' ] );
```

### `assertSpyWasCalledBefore( $spy, $other_spy )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
$other_spy = Spy::get_spy_for( 'say_goodbye' );
say_hello();
say_goodbye();
$this->assertSpyWasCalledBefore( $spy, $other_spy );
```

### `assertSpyWasNotCalledBefore( $spy, $other_spy )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
$other_spy = Spy::get_spy_for( 'say_goodbye' );
say_goodbye();
say_hello();
$this->assertSpyWasNotCalledBefore( $spy, $other_spy );
```

### `assertSpyWasCalledWhen( $spy, $callable )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'foo' );
$this->assertSpyWasCalledWhen( $spy, function( $args ) {
	return ( $args[0] === 'foo' );
} );
```

### `assertSpyWasNotCalledWhen( $spy, $callable )`

```php
$spy = Spy::get_spy_for( 'say_hello' );
say_hello( 'bar' );
$this->assertSpyWasNotCalledWhen( $spy, function( $args ) {
	return ( $args[0] === 'foo' );
} );
```

