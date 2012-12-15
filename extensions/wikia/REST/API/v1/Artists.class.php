<?php
namespace REST\API\v1;

class Artists extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $name = null ) {
		if ( $name === null ) {
			throw new \Exception( 'Missing parameter "name"' );
		}

		wfProfileIn( __METHOD__ );

		$dbr = wfGetDB( DB_SLAVE );
		$namespaces = array( NS_MAIN, NS_GRACENOTE );

		$rows = $dbr->select(
			'page',
			'*',
			array(
				'page_namespace' => $namespaces,
				'page_title LIKE ' . $dbr->addQuotes( '%' . str_replace( array( '%', ':' ) , '_', self::formatName( $name ) ) . '%' ),
				'page_title NOT LIKE \'%:%\''
			),
			__METHOD__
		);

		$items = array();

		while ( $row = $dbr->fetchObject( $rows ) ) {
			$title = \Title::newFromRow( $row );

			if ( $title instanceof \Title && $title->exists() ) {
				$items[] = array(
					'name' => $title->getText(),
					'discography' => $title->getFullUrl()
				);
			}
		}

		$dbr->freeResult( $rows );

		wfProfileOut( __METHOD__ );
		return $items;
	}

	/**
	 * Taken from extensions/3rdparty/LyricWiki/server.php
	 */
	public static function formatName( $name ) {
		wfProfileIn( __METHOD__ );

		$artistName = rawurldecode( ucwords( $name ) );
		$artistName = preg_replace( '/([-\("\.])([a-z])/e', '"$1".strtoupper("$2")', $artistName );
		$artistName = str_replace( " ", "_", $artistName );

		wfProfileOut( __METHOD__ );
		return $artistName;
	}
}