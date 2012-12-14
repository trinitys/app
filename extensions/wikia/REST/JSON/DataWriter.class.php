<?php
namespace REST\JSON;

class DataWriter extends \REST\base\DataWriter {
	function __construct() {
		parent::__construct();
		$this->setContentType( 'text/json' );
		$this->setCharset( 'utf-8' );
	}

	public function toString() {
		return json_encode( $this->content );
	}
}