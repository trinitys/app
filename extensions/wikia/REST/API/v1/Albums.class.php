<?php
namespace REST\API\v1;

class Albums extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $year = null, $artistName = null, $albumTitle = null ) {
		if ( $year === null ) {
                        throw new \Exception( 'Missing parameter "year"' );
                }

		if ( $artistName === null ) {
			throw new \Exception( 'Missing parameter "artistName"' );
		}

		if ( $albumTitle === null ) {
                        throw new \Exception( 'Missing parameter "albumTitle"' );
                }

		wfProfileIn( __METHOD__ );

		$albumTitle = self::formatTitle( $albumTitle, $artistName, $year );
		$title = \Title::newFromText( $albumTitle, NS_MAIN );

		if ( $title instanceof \Title && $title->exists() ) {
			$page = \WikiPage::factory( $title );
			$redirectTarget = $page->getRedirectTarget();

			if ( $redirectTarget instanceof \Title ) {
				$page = \WikiPage::factory( $redirectTarget );
			}

			$text = $page->getRawText();
			$items = array();

			if ( preg_match_all( "/^#[^\[]*\[\[[^\]]+:([^\]\|]+).*$/m", $text, $matches, PREG_SET_ORDER ) > 0 ) {
				foreach ( $matches as $item ) {
					$items[] = array(
						'title' => $item[1],
						//TODO: create a way to turn a Route in a string
						//since this won't be valid for other protocols than HTTP
						//#hardcoding #FTW
						'lyrics' => wfExpandUrl( '/rest.php/v1/Songs/' . urlencode( $artistName ) . '/' . urlencode( trim( $item[1] ) ) )
					);
				}
			} else {
				wfProfileOut( __METHOD__ );
				throw new \Exception( 'Song list not found' );
			}

			wfProfileOut( __METHOD__ );
			return $items;
		} else {
			wfProfileOut( __METHOD__ );
			throw new \Exception( 'Album not found' );
		}
	}

	/**
	 * Taken from extensions/3rdparty/LyricWiki/server.php
	 */
	public static function formatTitle( $title, $artist = null, $year = null ) {
		wfProfileIn( __METHOD__ );

		if ( $year !== null ) {
			$title .= "_({$year})";
		}

		$songTitle = Songs::formatTitle( $title, $artist );

		wfProfileOut( __METHOD__ );
		return $songTitle;
	}

	public static function parseTitle( $title ) {
		wfProfileIn( __METHOD__ );

		$count = preg_match( "/^([^:]+):(.*) \(([0-9]{4,})\)$/", $title, $matches );

		if ( $count > 0 ) {
			$info = array(
				'artist' => $matches[1],
				'title' => $matches[2],
				'year' => $matches[3]
			);

			wfProfileOut( __METHOD__ );
			return $info;
		} else {
			wfProfileOut( __METHOD__ );
			throw new \Exception( 'Incorrectly formatted title' );
		}
	}
}
