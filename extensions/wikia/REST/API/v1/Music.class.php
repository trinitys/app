<?php
namespace REST\API\v1;

class Music extends \REST\base\Resource implements \REST\base\Readable {
	const TYPE_ARTISTS = 'Artists';
	const TYPE_ALBUMS = 'Albums';
	const TYPE_SONGS = 'Songs';

	public function read( $type = null, $name = null ) {
		if ( $type === null ) {
			throw new \Exception( 'Missing parameter "type"' );
		}

		if ( !( $type == self::TYPE_ARTISTS || $type == self::TYPE_ALBUMS || $type == self::TYPE_SONGS ) ) {
			throw new \Exception( 'Unsupported value for parameter "type"' );
		}

		if ( $name === null ) {
			throw new \Exception( 'Missing parameter "name"' );
		}

		wfProfileIn( __METHOD__ );

		$dbr = wfGetDB( DB_SLAVE );
		$where = array( 'page_namespace' => array( NS_MAIN, NS_GRACENOTE ) );
		$parseClass = null;
		$processData = null;

		switch ( $type ) {
			case self::TYPE_ARTISTS:
				$where[] = 'page_title LIKE ' . $dbr->addQuotes( '%' . self::normalizeForSearch( Artists::formatTitle( $name ) ) . '%' );
				$where[] = 'page_title NOT LIKE \'%:%\'';

				$processData = function( \Title $title ) {
					return array(
						'name' => $title->getText(),
						'albums' => $title->getFullUrl()
					);
				};
				break;
			case self::TYPE_ALBUMS:
				$where[] = 'page_title LIKE ' . $dbr->addQuotes( '%:%' . self::normalizeForSearch( Albums::formatTitle( $name ) ) . '%\_(____)'
				);

				$processData = function( \Title $title ) {
					//TODO: add granularity by throwing a different
					//kind of exception when the data in the parentesis is
					//not an year in Albums::parseTitle
					try {
						$info = \REST\API\v1\Albums::parseTitle( $title->getText() );
					} catch( \Exception $e ) {
						//many songs have stuff in parentesis
						//at the end like (feat. XYZ)
						return null;
					}

					return array(
						'title' => $info['title'],
						'artist' => $info['artist'],
						'year' => $info['year'],
						'songs' => $title->getFullUrl()
					);
				};
				break;
			case self::TYPE_SONGS:
				$where[] = 'page_title LIKE ' . $dbr->addQuotes( '%:%' . self::normalizeForSearch( Songs::formatTitle( $name ) ) . '%' );
				$where[] = 'page_title NOT LIKE \'%\_(____)\'';

				$processData = function( \Title $title ) {
					$info = \REST\API\v1\Songs::parseTitle( $title->getText() );

					return array(
						'title' => $info['title'],
						'artist' => $info['artist'],
						//TODO: create a way to turn a Route in a string
						//since this won't be valid for other protocols than HTTP
						//#hardcoding #FTW
						'lyrics' => wfExpandUrl( '/rest.php/v1/Songs/' . urlencode($info['artist']) . '/' . urlencode( $info['title'] ) ) 
					);
				};
				break;
		}

		$rows = $dbr->select(
			'page',
			'*',
			$where,
			__METHOD__
		);

		$items = array();

		while ( $row = $dbr->fetchObject( $rows ) ) {
			$title = \Title::newFromRow( $row );

			if ( $title instanceof \Title && $title->exists() ) {
				$info = $processData( $title );

				if ( $info !== null ) {
					$items[] = $info;
				}
			}
		}

		$dbr->freeResult( $rows );

		wfProfileOut( __METHOD__ );
		return $items;
	}

	static private function normalizeForSearch( $value ) {
		return str_replace(
			array( '_', '%', ':' ) ,
			array( '\_', '\%', '_' ),
			$value
		);
	}
}
