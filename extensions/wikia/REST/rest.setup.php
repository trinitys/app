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

/**
 * base classes
 */
$wgAutoloadClasses['REST\base\Route'] = __DIR__ . "/base/Route.class.php";
$wgAutoloadClasses['REST\base\DataReader'] = __DIR__ . "/base/DataReader.class.php";
$wgAutoloadClasses['REST\base\Resource'] = __DIR__ . "/base/Resource.class.php";

/**
 * base interfaces
 */
$wgAutoloadClasses['REST\base\Creatable'] = __DIR__ . "/base/Creatable.interface.php";
$wgAutoloadClasses['REST\base\Readable'] = __DIR__ . "/base/Readable.interface.php";
$wgAutoloadClasses['REST\base\Updatable'] = __DIR__ . "/base/Updatable.interface.php";
$wgAutoloadClasses['REST\base\Deletable'] = __DIR__ . "/base/Deletable.interface.php";

/**
 * HTTP classes
 */
$wgAutoloadClasses['REST\HTTP\Route'] = __DIR__ . "/HTTP/Route.class.php";
$wgAutoloadClasses['REST\HTTP\DataReader'] = __DIR__ . "/HTTP/DataReader.class.php";

/**
 * API modules
 */
$wgAutoloadClasses['REST\API\v1\Dummy'] = __DIR__ . "/API/v1/Dummy.class.php";