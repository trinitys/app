<?php
namespace REST\base;

abstract class DataReader {
	protected $values;

	function __construct() {
		$this->values = array();
	}

	public function getValues( Array $values = null ) {
		if ( is_null( $values ) ) {
			return $this->values;
		} else {
			$result = array();

			foreach ( $values as $key => $default ) {
				$result[$key] = ( array_key_exists( $key, $this->values ) ) ? $this->values[$key] : $default;
			}

			return $result;
		}
	}

	public function getValue( $name, $default ) {
		if ( is_string( $name ) ) {
			return $this->getValues( array( $name => $default ) )[0];
		} else {
			throw new Exception( 'The name of the parameter to fetch must be a string' );
		}
	}
}