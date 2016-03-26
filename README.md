# Spies

A library to make testing in PHP so much easier.

Inspired by [Sinon](http://sinonjs.org/) in JavaScript, this library defines *Spies*, *Stubs*, and *Mocks* which can be used to mock and stub out methods (even globally defined functions, I'm looking at you, WordPress) and find out if they have been called.

Spies was also inspired by the excellent [WP_Mock](https://github.com/10up/wp_mock), [Mockery](http://docs.mockery.io/), and PHPUnit's own [getMockBuilder](https://phpunit.de/manual/current/en/test-doubles.html). These are all wonderful tools in their own right and without them Spies count not exist.

What does Spies offer that these other libraries do not?

**The very foundation of Spies is to make the syntax as easy to read as natural language.**

# Usage

If you want to just skip to the functions, you can [read the API here](API.md).

## Spies

A spy is like a function which does nothing. When it is called, the call is recorded including any arguments used. Later you can query the Spy to see how it was called.

To create a spy just create a new instance of `\Spies\Spy`, like this:
```php
$spy = new \Spies\Spy();
```

Then you can ask the spy how it was used:
```php
$spy = new \Spies\Spy();
$spy( 'hello', 'world' );

$spy->was_called(); // Returns true
$spy->was_called_times( 1 ); // Returns true
$spy->was_called_times( 2 ); // Returns false
$spy->get_times_called(); // Returns 1
$spy->was_called_with( 'hello', 'world' ); // Returns true
$spy->was_called_with( 'goodbye', 'world' ); // Returns false
```

This is useful for a number of reasons. If your code uses functions with dependency injection, you can pass the spy as an argument to another function to verify the other function's behavior.

```php
function addition( $adder, $a, $b ) {
	call_user_func( $adder, $a, $b );
}

$spy = new \Spies\Spy();
addition( $spy, 2, 3 );

$spy->was_called_with( 2, 3 ); // Returns true
```

If you are using a Spy in a PHPUnit test, it's even better if you use an *Expectation* (described more below):

```php
function addition( $adder, $a, $b ) {
	call_user_func( $adder, $a, $b );
}

function test_calculation() {
	$spy = new \Spies\Spy();
	addition( $spy, 2, 3 );

	$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 2, 3 ); // Passes
	$expectation->verify();
}
```

If you  want to create a Spy for a global function (a function in the global namespace, like WordPress's `wp_insert_post`), you can pass the name of the global function to `\Spies\get_spy_for()`:

```php
function test_calculation() {
	$add_one = \Spies\get_spy_for( 'add_together' );

	add_together( 2, 3 );

	$expectation = \Spies\expect_spy( $add_one )->to_have_been_called->with( 2, 3 ); // Passes
	$expectation->verify();
}
```

You can Spy on functions defined within a namespace in the same way:

```php
function test_calculation() {
	$add_one = \Spies\get_spy_for( '\Calculator\add_together' );

	\Calculator\add_together( 2, 3 );

	$expectation = \Spies\expect_spy( $add_one )->to_have_been_called->with( 2, 3 ); // Passes
	$expectation->verify();
}
```

## Stubs and Mocks

You can create stubs with the `\Spies\stub_function()` method. A stub is a fake function that can be called like a real function except that you control its behavior.

Stubs can also be used to mock a global function or a namespaced function, just like a Spy. In fact, a stub is also a Spy, which means you can query it for any information you like.

There are a few basic behaviors you can program into a stub:

1. You can simply use one to replace a global function (it will return null).
2. You can use one to return a specific value when called.
3. You can use one to return a specific value when called with specific arguments.
4. You can use one to return one of the arguments it was passed.
5. You can use one to call a substitute function.

Here's just setting a return value:
```php
\Spies\stub_function( 'get_color' )->and_return( 'green' );

get_color(); // Returns 'green'
```

Here's returning a value with certain arguments:
```php
\Spies\stub_function( 'add_one' )->when_called->with( 5 )->will_return( 6 );
\Spies\stub_function( 'add_one' )->when_called->with( 1 )->will_return( 2 );

add_one( 5 ); // Returns 6
add_one( 1 ); // Returns 2
```

Here's one returning one of its arguments:
```php
\Spies\stub_function( 'get_first' )->when_called->will_return( \Spies\passed_arg( 0 ) );

get_first( 5, 6, 7 ); // Returns 5
get_first( 1, 2, 3 ); // Returns 1
```

Here's one returning the result of a substitute function:
```php
\Spies\stub_function( 'add_one' )->and_return( function( $a ) {
	return $a + 1;
} );

add_one( 5 ); // Returns 6
add_one( 1 ); // Returns 2
```

# Objects

Sometimes you need to create a whole object with stubs as functions. In that case you can use `\Spies\mock_object()` which will return an object that can be passed around. The object by default has no methods, but you can use `add_method()` to add some.

`add_method()`, when called without a second argument, returns a stub (which, remember, is also a Spy), so you can program its behavior or query it for expectations. You can also use the second argument to pass a function (or Spy) explicitly, in which case whatever you pass is what will be returned.

```php
function test_calculation() {
	$adder = \Spies\mock_object();
	$add_one = $adder->add_method( 'add_one' )->that_returns( 5 );
	$add_one = $adder->add_method( 'add_one' )->when_called->with( 6 )->will_return( 7 );

	$calculator = new Calculator( $adder );
	$calculator->add_one( 4 ); // Returns 5
	$calculator->add_one( 6 ); // Returns 7

	\Spies\expect_spy( $add_one )->to_have_been_called(); // Passes
	\Spies\expect_spy( $add_one )->to_have_been_called->with( 2 ); // Fails
	\Spies\finish_spying(); // Verifies all Expectations
}
```

It can be tedious to call `add_method()` for every public method of an existing class that you are trying to mock. For that reason you can use `\Spies\mock_object_of( $class_name )` which will create a `MockObject` and automatically add a Spy for each public method on the original class. These Spies will all return null by default, but you can replace any of them with your own Spy by using `add_method()` as above.

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
	$mock = \Spies\mock_object_of( 'Greeter' );
	$mock->add_method( 'say_hello' )->that_returns( 'greetings' );
	$this->assertEquals( 'greetings', $mock->say_hello() );
	$this->assertEquals( null, $mock->say_goodbye() );
}
```

# Expectations

Spies can be useful all by themselves, but Spies also provides the `Expectation` class to make writing your test expectations easier.

Let's say we have a Spy and want to verify if it has been called in a PHPUnit test:
```php
function test_spy_is_called() {
	$spy = \Spies\make_spy();
	$spy();
	$this->assertTrue( $spy->was_called() );
}
```

That works, but here's another way to write it:
```php
function test_spy_is_called() {
	$spy = \Spies\make_spy();
	$spy();
	$expectation = \Spies\expect_spy( $spy )->to_have_been_called();
	$expectation->verify();
}
```

They're both totally valid. Expectations just add some more syntactic sugar to your tests and speed the debugging process by improving failure messages. Particularly, they allow building up a set of expected behaviors and then validating all of them at once. Let's use a more complex example. Here it is with just a Spy:

```php
function test_spy_is_called_correctly() {
	$spy = \Spies\make_spy();
	$spy( 'hello', 'world', 7 );
	$spy( 'hello', 'world', 8 );
	$this->assertTrue( $spy->was_called_with( 'hello', 'world', \Spies\any() ) );
	$this->assertTrue( $spy->was_called_times( 2 ) );
}
```

And here with Expectations:

```php
function test_spy_is_called_correctly() {
	$spy = \Spies\make_spy();
	$spy( 'hello', 'world', 7 );
	$spy( 'hello', 'world', 8 );
	$expectation = \Spies\expect_spy( $spy )->to_have_been_called->with( 'hello', 'world', \Spies\any() )->twice();
	$expectation->verify();
}
```

That last part, `$expectation->verify()` is what actually tests all the expected behaviors. You can also call the function `\Spies\finish_spying()` which will do the same thing, and can be put in a `tearDown` method.

## Better failures

Perhaps the most useful thing about Expectations is that they provide better failure messages. Whereas `$this->assertTrue( $spy->was_called_with( 'hello' ) )` and `\Spies\expect_spy( $spy )->to_have_been_called->with( 'hello' )` both assert the same thing, the former will only tell you "false is not true", and the Expectation will fail with something like this message:

```
Expected "anonymous function" to be called with ['hello'] but instead it was called with ['goodbye']
```

## finish_spying

To complete an expectation during a test, and to keep functions in the global scope from interfering with one another, it's **very important** to call `\Spies\finish_spying()` after each test.

`finish_spying()` does three things:

1. Calls `verify()` on each Expectation. `expect_spy()` only prepares the expectation. It is not tested until `verify()` is called.
2. Clears all current Spies and mocked functions.
3. Clears all current Expectations.

Because Expectations are only evaluated when we call `verify()` or `finish_spying()`, you can use expectations before or after the code that is being tested. There's syntactic sugar to make it sound right either way. The following two are the same:

```php
function tearDown() {
	\Spies\finish_spying();
}

function test_calculation() {
	$add_one = \Spies\get_spy_for( 'add_together' );

	add_together( 2, 3 );

	\Spies\expect_spy( $add_one )->to_have_been_called->with( 2, 3 ); // Passes
}
```

```php
function tearDown() {
	\Spies\finish_spying();
}

function test_calculation() {
	$add_one = \Spies\get_spy_for( 'add_together' );

	\Spies\expect_spy( $add_one )->to_be_called->with( 2, 3 ); // Passes

	add_together( 2, 3 );
}
```

## Argument lists

If you use `with()` to test an Expectation, sometimes you don't care about the value of an argument. In this case you can use `\Spies\Expectation::any()` in place of that argument:

```php
function tearDown() {
	\Spies\finish_spying();
}

function test_calculation() {
	$add_one = \Spies\get_spy_for( 'add_together' );

	\Spies\expect_spy( $add_one )->to_be_called->with( \Spies\Expectation::any(), \Spies\Expectation::any() ); // Passes

	add_together( 2, 3 );
}
```

# Comparison to other Mocking libraries

Other mocking libraries in PHP are great and each has its own strengths. Spies is intended to have readabilitiy as its main focus.

## More clear expectations

Let's say we want to mock `wp_insert_post` to return `4` and also make sure it is called only once with a particular set of arguments.

Here's how I'd do that with `WP_Mock` (using the `Mockery` syntax):
```php
WP_Mock::userFunction( 'wp_insert_post' )->with( $new_post_args )->andReturn( 4 )->once();
```

Here's the same thing using `Spies`:
```php
\Spies\mock_function( 'wp_insert_post' )->when_called->with( $new_post_args )->will_return( 4 );

$wp_insert_post = \Spies\get_spy_for( 'wp_insert_post' );
\Spies\expect_spy( $wp_insert_post )->to_be_called->with( $new_post_args )->once();
```

More verbose? Certainly. But I think you can see the difference between the mock and the expectation much more easily.

## More clear failures

Also, in many cases test failures are much easier to understand and give more information about what actually happened.

Let's say we mock a global function `foobar` to receieve the argument '4', but it is instead called with '7'.

With `WP_Mock` I'd write:
```php
public function test_thing() {
	WP_Mock::setUp();
	WP_Mock::userFunction( 'foobar' )->with( 4 );
	foobar( 7 );
	WP_Mock::tearDown();
}
```

With `Spies` I'd write:
```php
public function test_thing() {
	$foobar = \Spies\get_spy_for( 'foobar' );
	foobar( 7 );
	\Spies\expect_spy( $foobar )->to_have_been_called->with( 4 );
	\Spies\finish_spying();
}
```

Here's the failure with `WP_Mock`:

```
Mockery\Exception\NoMatchingExpectationException: No matching handler found for Mockery_2__wp_api::foobar(7). Either the method was unexpected or its arguments matched no expected argument list for this method
```

Here's the same failure with `Spies`:

```
Expected "foobar" to be called with [4] but instead it was called with [7]
```
