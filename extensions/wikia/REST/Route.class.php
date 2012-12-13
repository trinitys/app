<?php
namespace REST;

abstract class Route {
	private static $allowedActions = array(
		'create',
		'read',
		'update',
		'delete'
	);

	protected $resource;
	protected $action;

	function __construct( $resource, $action ) {
		$this->setResource( $resource );
		$this->setAction( $action );
	}

	public function setResource( $resource ) {
		if ( !empty( $resource ) && is_string( $resource ) ) {
			$this->resource = $resource;
		} else {
			throw new Exception( 'Cannot set resource to empty or non-string values' );
		}
	}

	public function getResource() {
		return $this->resource;
	}

	public function setAction( $action ) {
		if ( !empty( $action ) && is_string( $action ) ) {
			if ( in_array( $action, self::$allowedActions ) ) {
				$this->action = $action;
			} else {
				throw Exception( "{$action} is not allowed" );
			}
		} else {
			throw new Exception( 'Cannot set action to empty or non-string values' );
		}
	}

	public function getAction() {
		return $this->action;
	}

	//TODO: force type to DataReader
	public function resolve( $data ) {
		//TODO: resolve the route to an API module

		//TODO: return instance of DataWriter
		return array( 1, 2, 3 );
	}
}