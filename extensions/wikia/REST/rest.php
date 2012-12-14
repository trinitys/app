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

//initialize MW
require ( __DIR__ . '/../../../includes/WebStart.php' );

//setup profiler
if ( $wgProfiler instanceof Profiler ) {
	$wgProfiler->setTemplated( true );
}

$route = new REST\HTTP\Route();
$reader = new REST\HTTP\DataReader( $route->getAction() );
$writer = new REST\JSON\DataWriter();
$router = new REST\Router();

$router->setRoute( $route );
$router->setReader( $reader );
$router->setWriter( $writer );

$router->run();

header( "Content-Type: {$writer->getContentType()}; charset={$writer->getCharset()}" );
echo( $writer->toString() );

//print out profiling data if requested
wfLogProfilingData();