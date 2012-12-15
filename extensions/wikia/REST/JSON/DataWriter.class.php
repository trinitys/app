<?php
namespace REST\JSON;

class DataWriter extends \REST\base\DataWriter {
	private $prettyPrint;

	function __construct( $prettyPrint = false ) {
		parent::__construct();
		$this->setContentType( 'text/json' );
		$this->setCharset( 'utf-8' );
		$this->prettyPrint = (bool) $prettyPrint;
	}

	public function setPrettyPrint( $prettyPrint ) {
		if ( is_bool( $prettyPrint ) ) {
			$this->prettyPrint = $prettyPrint;
		} else {
			throw new \Exception( 'Cannot set prettyPrint to non-boolean values' );
		}
	}

	public function getPrettyPrint() {
		return $this->prettyPrint;
	}

	public function toString() {
		return json_encode( $this->content, ( !empty( $this->prettyPrint ) ) ? JSON_PRETTY_PRINT : null );
	}
}