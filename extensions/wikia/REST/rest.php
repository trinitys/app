<?php
/**
 * REST entrypoint for the HTTP protocol
 *
 * @author Federico "Lox" Lucignano <federico@wikia-inc.com>
 */

if ( $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	header ( "HTTP/1.1 200", true, 200 );
	return;
}

// Initialise common MW code
require ( __DIR__ . '/../../../includes/WebStart.php' );

if ( $wgProfiler instanceof Profiler ) {
	$wgProfiler->setTemplated( true );
}

$route = new REST\HTTP\Route();
$data = new REST\HTTP\DataReader();
$router = new REST\Router();

$router->setRoute( $route );
$router->setData( $data );
$result = $router->run();

var_dump( $result );