<?php
namespace REST\API\v1;

class Albums extends \REST\base\Resource implements \REST\base\Readable {
	public function read(){
		return null;
	}

	/**
	 * Taken from extensions/3rdparty/LyricWiki/server.php
	 */
	public static function formatTitle( $title, $artist = null, $year = null ) {
		wfProfileIn( __METHOD__ );

		if ( $year !== null ) {
			$title += "_({$year})";
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