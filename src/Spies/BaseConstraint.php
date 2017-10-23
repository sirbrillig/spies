<?php

namespace Spies;

if ( ! class_exists( 'PHPUnit_Framework_Constraint' ) ) {
	class_alias( \PHPUnit\Framework\Constraint\Constraint::class, 'PHPUnit_Framework_Constraint' );
}

abstract class BaseConstraint extends \PHPUnit_Framework_Constraint {
}
