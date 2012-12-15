<?php
namespace REST\API\v1;

class Artists extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $name = null ){
		if ( $name === null ) {
			throw new \Exception( 'Missing parameter "name"' );
		}

		return null;
	}

	/**
	 * Taken from extensions/3rdparty/LyricWiki/server.php
	 */
	public static function formatTitle( $title ) {
		wfProfileIn( __METHOD__ );

		$artistName = rawurldecode( ucwords( $title ) );
		$artistName = preg_replace( '/([-\("\.])([a-z])/e', '"$1".strtoupper("$2")', $artistName );
		$artistName = str_replace( " ", "_", $artistName );

		wfProfileOut( __METHOD__ );
		return $artistName;
	}
}