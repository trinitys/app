<?php
namespace REST\API\v1;

class Dummy extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $a = 1, $b = 2 ) {
		extract( $this->data->getValues( array( 'a' => $a, 'b' => $b ) ) );

		$total = $a * $b;

		return "{$a} x {$b} = {$total}, Yo!";
	}
}