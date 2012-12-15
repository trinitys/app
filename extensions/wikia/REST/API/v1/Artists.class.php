<?php
namespace REST\API\v1;

class Artists extends \REST\base\Resource implements \REST\base\Readable {
	public function read( $artistName = null ) {
		if ( $artistName === null ) {
			throw new \Exception( 'artistName is required');
		}

		$dbr = wfGetDB( DB_SLAVE );
		$namespaces = array( NS_MAIN, NS_GRACENOTE );

		$rows = $dbr->select(
			'page',
			'*',
			array(
				'page_namespace' => $namespaces,
				'page_title LIKE ' . $dbr->addQuotes( str_replace( ' ', '_', ucwords( $artistName ) ) )
			),
			__METHOD__
			/*OPTIONS*/
		);

		$items = array();

		while ( $row = $dbr->fetchObject( $rows ) ) {
			$t = \Title::newFromRow( $row );

			if ( $t instanceof \Title && $t->exists() ) {
				$items[] = array(
					'title' => $t->getText(),
					'url' => $t->getFullUrl()
				);
			}
		}

		$dbr->freeResult( $rows );
		return $items;
	}
}