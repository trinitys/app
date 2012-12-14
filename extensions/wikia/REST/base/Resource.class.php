<?php
namespace REST\base;

abstract class Resource {
	protected $data;

	function __construct(){
		$this->data = null;
	}

	public function setData( DataReader $data ) {
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
	}
}