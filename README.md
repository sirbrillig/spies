# Spies

A library to make testing in PHP so much easier.

Inspired by [Sinon](http://sinonjs.org/) in JavaScript, this library defines
*Spies*, *Stubs*, and *Mocks* which can be used to mock and stub out methods (even
globally defined functions, I'm looking at you, WordPress) and find out if they
have been called.

Best of all, the syntax is as easy to read as natural language, which is more
than I can say for some of the other PHP mocking libraries that I've used...

## More clear expectations

Let's say we want to mock `wp_insert_post` to return `4` and also make sure it is called only once with a particular set of arguments.

Here's WP_Mock (using the Mockery syntax):
```php
WP_Mock::userFunction( 'wp_insert_post' )->with( $new_post_args )->andReturn( 4 )->once();
```

Here's Spies:
```php
mock_function( 'wp_insert_post' )->with( $new_post_args )->and_return( 4 );

$wp_insert_post = get_spy_for( 'wp_insert_post' );
expect_spy( $wp_insert_post )->to_be_called->with( $new_post_args )->once();
```

More verbose? Certainly. But you can see what's going on much more easily.

## More clear failures

Also, in many cases test failures are much easier to understand.

Here's a failure with WP_Mock:

```
1) MyTest::test_insert_posts | makes each post with sticky set a sticky post | examples index 0
Mockery\Exception\NoMatchingExpectationException: No matching handler found for Mockery_2__wp_api::stick_post(7). Either the method was unexpected or its arguments matched no expected argument list for this method
```

Here's the same failure with Spies:

```
1) MyTest::test_insert_posts | makes each post with sticky set a sticky post | examples index 0
Expected stick_post to be called with [4] but instead it was called with [7]
```

