# Spies

A library to make testing in PHP so much easier.

Inspired by [Sinon](http://sinonjs.org/) in JavaScript, this library defines
*Spies*, *Stubs*, and *Mocks* which can be used to mock and stub out methods (even
globally defined functions, I'm looking at you, WordPress) and find out if they
have been called.

Best of all, the syntax is as easy to read as natural language, which is more
than I can say for some of the other PHP mocking libraries that I've used...

# Usage

## Spies

A spy is like a function which does nothing. When it is called, the call is
recorded including any arguments used. Later you can query the Spy to see how it
was called.

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

This is useful because you can pass the spy as an argument to another function
to verify the other function's behavior.

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

	\Spies\Expectation::expect_spy( $spy )->to_have_been_called->with( 2, 3 ); // Passes
}
```

If you  want to create a Spy for a global function (a function in the global
namespace, like WordPress's `wp_insert_post`), you can pass the name of the
global function to `\Spies\Spy::get_spy_for()`:

```php
function test_calculation() {
	$add_one = \Spies\Spy::get_spy_for( 'add_together' );

	add_together( 2, 3 );

	\Spies\Expectation::expect_spy( $add_one )->to_have_been_called->with( 2, 3 ); // Passes
}
```

## Stubs

```php
\Spies\Spy::stub_function( 'get_color' )->and_return( 'green' );

get_color(); // Returns 'green'
```

```php
\Spies\Spy::stub_function( 'add_one' )->with( 5 )->and_return( 6 );

add_one( 5 ); // Returns 6
```

## Mocks

```php
\Spies\Spy::stub_function( 'add_one' )->and_return( function( $a ) {
	return $a + 1;
} );

add_one( 5 ); // Returns 6
```

# Objects

```php
function test_calculation() {
	$adder = \Spies\MockObject::mock_object();
	$add_one = $adder->add_method( 'add_one' )->that_returns( 5 );

	$calculator = new Calculator( $adder );
	$calculator->add_one( 4 ); // Returns 5

	\Spies\Expectation::expect_spy( $add_one )->to_have_been_called(); // Passes
	\Spies\Expectation::expect_spy( $add_one )->to_have_been_called->with( 2 ); // Fails
}
```

# Expectations

To complete an expectation during a test, and to keep functions in the global
scope from interfering with one another, it's **very important** to call
`\Spies\Expectation::finish_spying()` after each test.

You can use expectations before or after the code that is being tested. There's
syntactic sugar to make it sound right either way. The following two are
the same:

```php
function tearDown() {
	\Spies\Expectation::finish_spying();
}

function test_calculation() {
	$add_one = \Spies\Spy::get_spy_for( 'add_together' );

	add_together( 2, 3 );

	\Spies\Expectation::expect_spy( $add_one )->to_have_been_called->with( 2, 3 ); // Passes
}
```

```php
function tearDown() {
	\Spies\Expectation::finish_spying();
}

function test_calculation() {
	$add_one = \Spies\Spy::get_spy_for( 'add_together' );

	\Spies\Expectation::expect_spy( $add_one )->to_be_called->with( 2, 3 ); // Passes

	add_together( 2, 3 );
}
```

# Comparison to other Mocking libraries

## More clear expectations

Let's say we want to mock `wp_insert_post` to return `4` and also make sure it is called only once with a particular set of arguments.

Here's `WP_Mock` (using the `Mockery` syntax):
```php
WP_Mock::userFunction( 'wp_insert_post' )->with( $new_post_args )->andReturn( 4 )->once();
```

Here's `Spies`:
```php
mock_function( 'wp_insert_post' )->with( $new_post_args )->and_return( 4 );

$wp_insert_post = get_spy_for( 'wp_insert_post' );
expect_spy( $wp_insert_post )->to_be_called->with( $new_post_args )->once();
```

More verbose? Certainly. But you can see what's going on much more easily.

## More clear failures

Also, in many cases test failures are much easier to understand.

Here's a failure with `WP_Mock`:

```
Mockery\Exception\NoMatchingExpectationException: No matching handler found for Mockery_2__wp_api::stick_post(7). Either the method was unexpected or its arguments matched no expected argument list for this method
```

Here's the same failure with `Spies`:

```
Expected "stick_post" to be called with [4] but instead it was called with [7]
```
