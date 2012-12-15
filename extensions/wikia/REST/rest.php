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
require( __DIR__ . '/../../../includes/WebStart.php' );

//setup profiler
if ( $wgProfiler instanceof Profiler ) {
	$wgProfiler->setTemplated( true );
}


if ( function_exists( 'apache_request_headers' ) ) {
	$clientHeaders = apache_request_headers();
	$prettyPrint = (
		isset( $clientHeaders['Accept'] ) &&
		preg_match( '#^(text/html|application/xhtml+xml|application/xml)#i', $clientHeaders['Accept'] ) > 0
	);
} else {
	$prettyPrint = false;
}

$route = new REST\HTTP\Route();
$reader = new REST\HTTP\DataReader( $route->getAction() );
$writer = new REST\JSON\DataWriter( $prettyPrint );
$router = new REST\Router();

$router->setRoute( $route );
$router->setReader( $reader );
$router->setWriter( $writer );

$router->run();

header( "Content-Type: {$writer->getContentType()}; charset={$writer->getCharset()}" );
echo( $writer->toString() );

//print out profiling data if requested
wfLogProfilingData();