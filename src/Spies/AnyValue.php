<?php
namespace Spies;

class AnyValue {
	public $value = 'ANYTHING';

	public function __toString() {
		return 'AnyValue';
	}
}
