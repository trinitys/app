<?php
namespace REST\API\v1;

class Artists extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $artistName = null ) {
		if ( $artistName === null ) {
			throw new \Exception( 'Missing parameter "artistName"' );
		}

		wfProfileIn( __METHOD__ );
		$title = \Title::newFromText( self::formatTitle( $artistName ), NS_MAIN );

		if ( $title instanceof \Title && $title->exists() ) {
			$page = \WikiPage::factory( $title );
			$redirectTarget = $page->getRedirectTarget();

			if ( $redirectTarget instanceof \Title ) {
				$page = \WikiPage::factory( $redirectTarget );
			}

			$text = $page->getRawText();
			$items = array();

			if ( preg_match_all( "/^==\[\[[^\]]+:([^\(]+)\(([0-9]{4,})\).*\]\]==$/mU", $text, $matches, PREG_SET_ORDER ) > 0 ) {
				foreach ( $matches as $item ) {
					$items[] = array(
						'title' => $item[1],
						//TODO: create a way to turn a Route in a string
						//since this won't be valid for other protocols than HTTP
						//#hardcoding #FTW
						'songs' => wfExpandUrl( '/rest.php/v1/Albums/' . urlencode( trim( $item[2] ) ) . '/' . urlencode( $artistName ) . '/' . urlencode( trim( $item[1] ) ) )
					);
				}
			} else {
				wfProfileOut( __METHOD__ );
				throw new \Exception( 'Discography not found' );
			}

			wfProfileOut( __METHOD__ );
			return $items;
		} else {
			wfProfileOut( __METHOD__ );
			throw new \Exception( 'Artist not found' );
		}
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
