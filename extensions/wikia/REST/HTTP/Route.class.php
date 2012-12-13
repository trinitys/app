<?php
namespace REST\HTTP;

class Route extends \REST\Route {
	static private $actionMap = array(
		'POST' => 'create',
		'GET' => 'read',
		'PUT' => 'update',
		'DELETE' => 'delete'
	);

	function __construct() {
		$path = $_SERVER['PATH_INFO'];
		$method = $_SERVER['REQUEST_METHOD'];

		$path = '\REST\API' . str_replace( '/', '\\', $path );

		if ( in_array( $method, array_keys( self::$actionMap ) ) ) {
			$method = self::$actionMap[$method];
		}

		parent::__construct( $path, $method );
	}
}