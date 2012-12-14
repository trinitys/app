<?php
namespace REST\HTTP;

class Route extends \REST\base\Route {
	static private $actionMap = array(
		'POST' => 'create',
		'GET' => 'read',
		'PUT' => 'update',
		'DELETE' => 'delete'
	);

	function __construct() {
		$path = $_SERVER['PATH_INFO'];
		$method = $_SERVER['REQUEST_METHOD'];

		//only the first two parts of the path
		//indentify a resource (version and resource name),
		//the rest are scope paramenters
		$tokens = array_slice( explode( '/', $path ), 1, 2 );

		if ( count( $tokens ) > 1 ) {
			$path = '\REST\API\\' . implode( '\\', $tokens );

			if ( in_array( $method, array_keys( self::$actionMap ) ) ) {
				$method = self::$actionMap[$method];
			}

			parent::__construct( $path, $method );
		} else {
			throw new \Exception( 'Invalid path' );
		}
	}
}