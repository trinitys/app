<?php
namespace REST\API\v1;

class Dummy extends \REST\base\Resource
	implements \REST\base\Readable,
	\REST\base\Creatable,
	\REST\base\Updatable,
	\REST\base\Deletable {
	public function read( $a = 1, $b = 1, $c = 1 ) {
		$total = (int) $a * (int) $b;
		$total -= (int) $c;

		return "({$a} x {$b}) - {$c} = {$total}, Yo!";
	}

	public function create( $a = 1, $b = 1, $c = 1 ) {
		$total = (int) $a / (int) $b;
		$total += (float) $c;

		return "({$a} / {$b}) + {$c} = {$total}, Duh!";
	}

	public function update( $a = 1, $b = 1, $c = 1 ) {
		return "{$a} -- {$b} -- {$c}, Meh!";
	}

	public function delete( $a = 1, $b = 1, $c = 1 ) {
		return "{$a} ## {$b} ## {$c}, Yuck!";
	}
}