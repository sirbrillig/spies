<?php
namespace Spies;

class Expectation {
	// Syntactic sugar; these just return the Expectation
	public $to_be_called;
	public $to_have_been_called;

	// Can be used to prevent double-verification.
	public $was_verified = false;

	private $spy = null;
	private $negation = null;
	private $expected_args = null;
	private $delayed_expectations = [];

	public function __construct( $spy ) {
		if ( is_string( $spy ) ) {
			throw new \InvalidArgumentException( 'Expectations require a Spy but I was passed a string: ' . $spy );
		}
		if ( ! $spy instanceof Spy ) {
			throw new \InvalidArgumentException( 'Expectations require a Spy' );
		}
		$this->spy = $spy;
		$this->to_be_called = $this;
		$this->to_have_been_called = $this;
		GlobalExpectations::add_expectation( $this );
	}

	public static function expect_spy( $spy ) {
		return new Expectation( $spy );
	}

	public static function any() {
		return new AnyValue();
	}

	/**
	 * Magic function so that the `not` property can be used to negate this
	 * Expectation.
	 */
	public function __get( $key ) {
		if ( $key === 'not' ) {
			$this->negation = true;
			return $this;
		}
		throw new InvalidExpectationException( 'Invalid property: "' . $key . '" does not exist on this Expectation' );
	}

	/**
	 * Alias for get_fail_message
	 *
	 * @return string|null The first failure description if there is a failure
	 */
	public function verify() {
		return $this->get_fail_message();
	}

	/**
	 * Return true if all behaviors in this Expectation are met
	 *
	 * @return boolean True if the behaviors are all met
	 */
	public function met_expectations() {
		$message = $this->get_fail_message();
		return empty( $message );
	}

	/**
	 * Return the first failure message for the behaviors on this Expectation
	 *
	 * Returns null if no behaviors failed.
	 *
	 * @return string|null The first failure message for the behaviors on this Expectation or null
	 */
	public function get_fail_message() {
		$this->was_verified = true;
		foreach( $this->delayed_expectations as $behavior ) {
			$description = call_user_func( $behavior );
			if ( $description !== null ) {
				return 'Failed asserting that ' . $description;
			}
		}
		return null;
	}

	/**
	 * Set expected behavior
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * The passed function will be called each time the spy is called and
	 * passed the arguments of that call.
	 *
	 * @param callable $callable The function to run on every call
	 * @return Expectation This Expectation to allow chaining
	 */
	public function when( $callable ) {
		$this->expected_function = $callable;
		$this->add_expectation_for_constraint( new SpiesConstraintWasCalledWhen( $this->expected_function, $this->negation ) );
		return $this;
	}

	/**
	 * Set expected arguments
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * If passed a function, it will be called each time the spy is called and
	 * passed the arguments of that call.
	 *
	 * @param mixed $arg... The arguments we expect or a function
	 * @return Expectation This Expectation to allow chaining
	 */
	public function with() {
		$args = func_get_args();
		if ( is_callable( $args[0] ) ) {
			return $this->when( $args[0] );
		}
		$this->expected_args = $args;
		$this->add_expectation_for_constraint( new SpiesConstraintWasCalledWith( $this->expected_args, $this->negation ) );
		return $this;
	}

	/**
	 * Set the expectation that the Spy was called
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function to_be_called() {
		$constraint = $this->negation ? new SpiesConstraintWasNotCalled() : new SpiesConstraintWasCalled();
		$this->add_expectation_for_constraint( $constraint );
		return $this;
	}

	private function add_expectation_for_constraint( $constraint ) {
		$this->delay_expectation( function() use ( $constraint ) {
			$does_constraint_match = $constraint->matches( $this->spy );
			return $does_constraint_match ? null : $constraint->failureDescription( $this->spy );
		} );
	}

	/**
 	 * Set the expectation that the Spy was called
	 *
	 * Alias for `to_be_called`
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function to_have_been_called() {
		$args = func_get_args();
		return call_user_func_array( [ $this, 'to_be_called' ], $args );
	}

	/**
	 * Set the expectation that the Spy was called once
	 *
	 * Alias for `times(1)`
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function once() {
		return $this->times( 1 );
	}

	/**
	 * Set the expectation that the Spy was called twice
	 *
	 * Alias for `times(2)`
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function twice() {
		return $this->times( 2 );
	}

	/**
	 * Set the expectation that the Spy was called a number of times
	 *
	 * Expectations will be evaluated when `verify()` is called.
	 *
	 * @return Expectation This Expectation to allow chaining
	 */
	public function times( $count ) {
		$constraint = new SpiesConstraintWasCalledTimes( $count, $this->negation );
		if ( isset( $this->expected_args ) ) {
			$constraint = new SpiesConstraintWasCalledTimesWith( $count, $this->expected_args, $this->negation );
		}
		$this->add_expectation_for_constraint( $constraint );
		return $this;
	}

	public function before( $target_spy ) {
		$this->add_expectation_for_constraint( new SpiesConstraintWasCalledBefore( $target_spy, $this->negation ) );
		return $this;
	}

	/**
 	 * Delay an expected behavior
	 *
	 * This will store a function to be run when `verify` is called on this
	 * Expectation. You can delay as many behavior functions as you like. Each
	 * behavior function should throw an Exception if it fails.
	 *
	 * @param function $behavior A function that describes the expected behavior
	 */
	private function delay_expectation( $behavior ) {
		$this->delayed_expectations[] = $behavior;
	}
}
