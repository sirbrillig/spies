# Spies API Reference

# functions

- `make_spy()`: Shortcut for `new Spy()`.
- `get_spy_for( $function_name )`: Spy on a global or namespaced function. Shortcut for `Spy::stub_function( $function_name )`.
- `stub_function( $function_name )`: Stub a global or namespaced function. Shortcut for `Spy::stub_function( $function_name )`.
- `mock_function( $function_name )`: Alias for `stub_function()`.
- `expect_spy( $spy )`: Shortcut for `Expectation::expect_spy( $spy )`.
- `mock_object()`: Shortcut for `MockObject::mock_object()`.
- `mock_object_of( $class_name )`: Mock an instance of an existing class with all its methods. Shortcut for `MockObject::mock_object( $class_name )`.
- `finish_spying()`: Resolve all global Expectations, then clear all Expectations and all global Spies. Shortcut for `GlobalExpectations::resolve_delayed_expectations()`, `GlobalExpectations::clear_all_expectations()`, and `GlobalSpies::clear_all_spies`.
- `any()`: Used as an argument to `Expectation->with()` to mean "any argument". Shortcut for `new AnyValue()`.
- `passed_arg( $index )`: Used as an argument to `Spy->and_return()` to mean "return the passed argument at $index". Shortcut for `new PassedArgument( $index )`.

# Spy

### Static methods

- `get_spy_for( $function_name )`: Create a new global or namespaced function and attach it to a new Spy, returning that Spy.

### Instance methods

- `get_function_name()`: Return the spy's function name. Really only useful when spying on global or namespaced functions. Defaults to "anonymous function".
- `set_function_name()`: Set the spy's function name. You generally don't need to use this.
- `call( $arg... )`: Call the Spy. It's probably easier to just call the Spy as a function.
- `call_with_array( $args )`: Call the Spy with an array of arguments. It's probably easier to just call the Spy as a function.
- `clear_call_record()`: Clear the Spy's call record.
- `get_called_functions()`: Get the raw call record for the Spy. Each call is an instance of `SpyCall`.
- `was_called()`: Return true if the Spy was called.
- `was_called_with( $arg... )`: Return true if the Spy was called with specific arguments.
- `was_called_when( $callable )`: Return true if the passed function returns true at least once. For each spy call, the function will be called with the arguments from that call.
- `was_called_times( $count )`: Return true if the Spy was called exactly $count times.
- `was_called_before( $spy )`: Return true if the Spy was called before $spy.
- `get_times_called()`: Return the number of times the Spy was called.
- `get_call( $index )`: Return the call record for a single call.

# Stub (Stubs are actually just instances of Spy used differently)

### Static methods

- `stub_function( $function_name )`: Create a new global or namespaced function and attach it to a new Spy, returning that Spy.

### Instance methods

- `and_return( $value )`: Instruct the stub to return $value when called. $value can also be a function to call when the stub is called.
- `will_return( $value )`: Alias for `and_return( $value )`.
- `that_returns( $value )`: Alias for `and_return( $value )`.
- `with( $arg... )`: Changes behavior of next `and_return()` to be a conditional return value.
- `when_called`: Syntactic sugar. Returns the Stub.
- `and_return_first_argument()`: Shortcut for `and_return( passed_arg( 0 ) )`.
- `and_return_second_argument()`: Shortcut for `and_return( passed_arg( 1 ) )`.

# SpyCall

## Instance methods

- `get_args()`: Return the arguments for a call.
- `get_timestamp()`: Return the timestamp for when a call was made.

# MockObject

### Static methods

- `mock_object()`: Shortcut for `new MockObject()`.
- `mock_object_of( $class_name )`: Create a new `MockObject`, automatically adding a Spy for every public method in `$class_name`.

### Instance methods

- `add_method( $function_name, $function = null )`: Add a public method to this Object as a Spy and return that method. Creates and returns a Spy if no function is provided.
- `spy_on_method( $function_name, $function = null )`: Alias for `add_method()`.
- `and_ignore_missing()`: Prevents throwing an Exception when an unmocked method is called on this object.

# Expectation

### Static methods

- `expect_spy( $spy )`: Create a new Expectation for the behavior of $spy.

### Instance methods

- `to_be_called`: Syntactic sugar. Returns the Expectation.
- `to_have_been_called`: Syntactic sugar. Returns the Expectation.
- `not`: When accessed, reverses all expected behaviors on this Expectation.
- `verify()`: Resolve and verify all the behaviors set on this Expectation.
- `to_be_called()`: Add an expected behavior that the spy was called when this is resolved.
- `to_have_been_called()`: Alias for `to_be_called()`.
- `with( $arg... )`: Add an expected behavior that the spy was called with particular arguments when this is resolved.
- `when( $callable )`: Return true if the passed function returns true at least once. For each spy call, the function will be called with the arguments from that call.
- `times( $count )`: Add an expected behavior that the spy was called exactly $count times.
- `once()`: Alias for `times( 1 )`.
- `twice()`: Alias for `times( 2 )`.
- `before( $spy )`: Add an expected behavior that the spy was called before $spy.

# PHPUnit Custom Assertions

These are methods available on instances of `\Spies\TestCase`.

### Constraints for `assertThat()`

- `wasCalled()`
- `wasNotCalled()`
- `wasCalledTimes( $count )`
- `wasCalledBefore( $spy )`
- `wasCalledWhen( $callable )`

### Assertions

- `assertSpyWasCalled( $spy )`
- `assertSpyWasNotCalled( $spy )`
- `assertSpyWasCalledWith( $spy, $args )`
- `assertSpyWasCalledTimes( $spy, $count )`
- `assertSpyWasNotCalledTimes( $spy, $count )`
- `assertSpyWasCalledBefore( $spy, $other_spy )`
- `assertSpyWasNotCalledBefore( $spy, $other_spy )`
- `assertSpyWasCalledWhen( $spy, $callable )`
- `assertSpyWasNotCalledWhen( $spy, $callable )`
