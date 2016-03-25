# Spies API Reference

## functions

- `make_spy()`: Shortcut for `new Spy()`.
- `get_spy_for( $function_name )`: Spy on a global or namespaced function. Shortcut for `Spy::stub_function( $function_name )`.
- `stub_function( $function_name )`: Stub a global or namespaced function. Shortcut for `Spy::stub_function( $function_name )`.
- `mock_function( $function_name )`: Alias for `stub_function()`.
- `expect_spy( $spy )`: Shortcut for `Expectation::expect_spy( $spy )`.
- `mock_object()`: Shortcut for `MockObject::mock_object()`.
- `finish_spying()`: Resolve all global Expectations, then clear all Expectations and all global Spies. Shortcut for `GlobalExpectations::resolve_delayed_expectations()`, `GlobalExpectations::clear_all_expectations()`, and `GlobalSpies::clear_all_spies`.
- `any()`: Used as an argument to `Expectation->with()` to mean "any argument". Shortcut for `new AnyValue()`.
- `passed_arg( $index )`: Used as an argument to `Spy->and_return()` to mean "return the passed argument at $index". Shortcut for `new PassedArgument( $index )`.

## Spy

- `get_function_name()`: Return the spy's function name. Really only useful when spying on global or namespaced functions. Defaults to "anonymous function".
- `set_function_name()`: Set the spy's function name. You generally don't need to use this.
- `call( $arg... )`: Call the Spy. It's probably easier to just call the Spy as a function.
- `call_with_array( $args )`: Call the Spy with an array of arguments. It's probably easier to just call the Spy as a function.
- `clear_call_record()`: Clear the Spy's call record.
- `get_called_functions()`: Get the raw call record for the Spy.
- `was_called()`: Return true if the Spy was called.
- `was_called_with( $arg... )`: Return true if the Spy was called with specific arguments.
- `was_called_times( $count )`: Return true if the Spy was called exactly $count times.
- `was_called_before( $spy )`: Return true if the Spy was called before $spy.

## Stub (also Spy)
