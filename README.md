# Spies

A library to make testing in PHP so much easier.

Inspired by [Sinon](http://sinonjs.org/) in JavaScript, this library defines *Spies*, *Stubs*, and *Mocks* which can be used to mock and stub out methods (even globally defined functions, I'm looking at you, WordPress) and find out if they have been called.

Spies was also inspired by the excellent [WP_Mock](https://github.com/10up/wp_mock), [Mockery](http://docs.mockery.io/), and PHPUnit's own [getMockBuilder](https://phpunit.de/manual/current/en/test-doubles.html). These are all wonderful tools in their own right, but some of them conflate mocking and expectation or have a syntax that I find non-intuitive.

**The very foundation of Spies is to make the syntax as easy to read as natural language.**

# Usage

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
$spy->was_called_with( 'hello', 'world' ); // Returns true
$spy->was_called_with( 'goodbye', 'world' ); // Returns false
```

This is useful because you can pass the spy as an argument to another function to verify the other function's behavior.

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

	\Spies\expect_spy( $spy )->to_have_been_called->with( 2, 3 ); // Passes
}
```

If you  want to create a Spy for a global function (a function in the global namespace, like WordPress's `wp_insert_post`), you can pass the name of the global function to `\Spies\get_spy_for()`:

```php
function test_calculation() {
	$add_one = \Spies\get_spy_for( 'add_together' );

	add_together( 2, 3 );

	\Spies\expect_spy( $add_one )->to_have_been_called->with( 2, 3 ); // Passes
}
```

## Stubs and Mocks

```php
\Spies\stub_function( 'get_color' )->and_return( 'green' );

get_color(); // Returns 'green'
```

```php
\Spies\stub_function( 'add_one' )->with( 5 )->and_return( 6 );

add_one( 5 ); // Returns 6
```

```php
\Spies\stub_function( 'add_one' )->and_return( function( $a ) {
	return $a + 1;
} );

add_one( 5 ); // Returns 6
```

# Objects

```php
function test_calculation() {
	$adder = \Spies\mock_object();
	$add_one = $adder->add_method( 'add_one' )->that_returns( 5 );

	$calculator = new Calculator( $adder );
	$calculator->add_one( 4 ); // Returns 5

	\Spies\expect_spy( $add_one )->to_have_been_called(); // Passes
	\Spies\expect_spy( $add_one )->to_have_been_called->with( 2 ); // Fails
}
```

# Expectations

To complete an expectation during a test, and to keep functions in the global scope from interfering with one another, it's **very important** to call `\Spies\finish_spying()` after each test.

`finish_spying()` does three things:

1. Calls `verify()` on each Expectation. `expect_spy()` only prepares the expectation. It is not tested until `verify()` is called.
2. Clears all current Spies and mocked functions.
3. Clears all current Expectations.

Because Expectations are only evaluated when we call `finish_spying()`, you can use expectations before or after the code that is being tested. There's syntactic sugar to make it sound right either way. The following two are the same:

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
