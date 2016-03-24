<?php

\Spies\Spy::add_behavior( 'will_return', function( $spy, $args ) {
	return $spy->and_return( $args[0] );
} );
