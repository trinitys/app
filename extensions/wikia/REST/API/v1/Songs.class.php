<?php
namespace REST\API\v1;

class Songs extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $title = null ) {
		if ( $title === null ) {
			throw new \Exception( 'Missing parameter "title"' );
		}

		wfProfileIn( __METHOD__ );

		$dbr = wfGetDB( DB_SLAVE );
		$namespaces = array( NS_MAIN, NS_GRACENOTE );

		$rows = $dbr->select(
			'page',
			'*',
			array(
				'page_namespace' => $namespaces,
				'page_title LIKE ' . $dbr->addQuotes( '%:%' .
					str_replace(
						array( '_', '%', ':' ) , array( '\_', '\%', '_' ), self::formatTitle( $title )
					) . '%' ),
				'page_title NOT LIKE \'%\_(____)\''
			),
			__METHOD__
		);

		$items = array();

		while ( $row = $dbr->fetchObject( $rows ) ) {
			$title = \Title::newFromRow( $row );

			if ( $title instanceof \Title && $title->exists() ) {
				$songInfo = self::parseTitle( $title->getText() );

				$items[] = array(
					'artist' => $songInfo['artist'],
					'title' => $songInfo['title'],
					'lyrics' => $title->getFullUrl()
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
	public static function formatTitle( $title ) {
		wfProfileIn( __METHOD__ );

		$songTitle = rawurldecode( ucwords( $title ) );
		$songTitle = preg_replace( '/([-\("\.])([a-z])/e', '"$1".strtoupper("$2")', $songTitle );
		$songTitle = str_replace( " ", "_", $songTitle );

		wfProfileOut( __METHOD__ );
		return $songTitle;
	}

	public static function parseTitle( $title ) {
		wfProfileIn( __METHOD__ );

		$count = preg_match( "/^([^:]+):(.*)$/", $title, $matches );

		if ( $count > 0 ) {
			$info = array(
				'artist' => $matches[1],
				'title' => $matches[2]
			);

			wfProfileOut( __METHOD__ );
			return $info;
		} else {
			wfProfileOut( __METHOD__ );
			throw new \Exception( 'Incorrectly formatted title' );
		}
	}
}