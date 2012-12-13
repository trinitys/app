<?php
/**
 * REST API
 *
 * Extension setup file
 *
 * @author Federico "Lox" Lucignano <federico@wikia-inc.com>
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of MediaWiki, it is not a valid entry point.\n";
	exit( 1 );
}

$dir = dirname( __FILE__ );

/**
 * info
 */
$wgExtensionCredits['other'] = array(
	"name" => "REST API",
	"description" => "REST API for MediaWiki",
	"author" => array(
		'Federico "Lox" Lucignano <federico@wikia-inc.com>'
	)
);


/**
 * core classes
 */
$wgAutoloadClasses['REST\Router'] = __DIR__ . "/Router.class.php";
$wgAutoloadClasses['REST\Route'] = __DIR__ . "/Route.class.php";

/**
 * HTTP classes
 */
$wgAutoloadClasses['REST\HTTP\Route'] = __DIR__ . "/HTTP/Route.class.php";