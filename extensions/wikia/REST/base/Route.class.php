<?php
namespace REST\base;

abstract class Route {
	private static $actionsList = array(
		'create' => 'REST\base\Creatable',
		'read' => 'REST\base\Readable',
		'update' => 'REST\base\Updatable',
		'delete' => 'REST\base\Deletable'
	);
	private static $allowedActions = null;

	protected $resource;
	protected $action;

	function __construct( $resource, $action ) {
		if ( is_null( self::$allowedActions ) ) {
			self::$allowedActions = array_keys( self::$actionsList );
		}

		$this->setResource( $resource );
		$this->setAction( $action );
	}

	public function setResource( $resource ) {
		if ( !empty( $resource ) && is_string( $resource ) ) {
			$this->resource = $resource;
		} else {
			throw new \Exception( 'Cannot set resource to empty or non-string values' );
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
				throw new \Exception( 'Action not allowed' );
			}
		} else {
			throw new \Exception( 'Cannot set action to empty or non-string values' );
		}
	}

	public function getAction() {
		return $this->action;
	}

	public function resolve( DataReader $data ) {
		$resource = $this->getResource();

		if ( class_exists( $resource ) ) {
			$action = $this->getAction();

			if ( in_array( self::$actionsList[$action], class_implements( $resource ) ) ) {
				$instance = new $resource();
				$instance->setData( $data );
				$result = $instance->$action();

				//TODO: return instance of DataWriter
				return $result;
			} else {
				throw new \Exception( 'The resource doesn\'t support the requested action' );
			}
		} else {
			throw new \Exception( 'The resource doesn\'t exist' );
		}
	}
}