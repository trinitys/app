<?php
namespace REST\API\v1;

class Songs extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $artistName = null, $songTitle = null ) {
		if ( $artistName === null ) {
			throw new \Exception( 'Missing parameter "artistName"' );
		}

		if ( $songTitle === null ) {
                        throw new \Exception( 'Missing parameter "songTitle"' );
                }

		wfProfileIn( __METHOD__ );

		$title = \Title::newFromText( self::formatTitle( $songTitle, $artistName ), NS_MAIN );

		if ( $title instanceof \Title && $title->exists() ) {
			$page = \WikiPage::factory( $title );
			$redirectTarget = $page->getRedirectTarget();

			if ( $redirectTarget instanceof \Title ) {
				$page = \WikiPage::factory( $redirectTarget );
			}

			if ( preg_match( "/<(lyrics|gracenotelyrics)>\s*(.*)\s*<\/\\1>/ims", $page->getRawText(), $matches ) > 0 ) {
				wfProfileOut( __METHOD__ );
				return trim( $matches[2] );
			} else {
				wfProfileOut( __METHOD__ );
				throw new \Exception( 'Lyrics not found' );
			}

			wfProfileOut( __METHOD__ );
			return $wikiText;
		} else {
			wfProfileOut( __METHOD__ );
			throw new \Exception( 'Song not found' );
		}
	}

	/**
	 * Taken from extensions/3rdparty/LyricWiki/server.php
	 */
	public static function formatTitle( $title, $artist = null ) {
		wfProfileIn( __METHOD__ );

		if ( $artist !== null ) {
			$title = "{$artist}:{$title}";
		}

		$songTitle = Artists::formatTitle( $title );

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
