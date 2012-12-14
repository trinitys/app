<?php
namespace REST\API\v1;

class Dummy implements \REST\base\Readable {
	public function read() {
		return "Yo!";
	}
}