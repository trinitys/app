<?php
namespace REST\base;

abstract class DataReader {
	protected $values;

	function __construct() {
		$this->values = array();
	}

	public function getValues() {
		return $this->values;
	}

	public function setValues( Array $values ) {
		$this->values = $values;
	}
}