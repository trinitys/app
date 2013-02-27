<?php

class WikiaSearchResultSetSingleton extends WikiaSearchResultSet
{
	public function __construct( WikiaSearchResult $result ) {
		global $wgLanguageCode;
		$this->results = array( $result );
		
		$cityId			= $result->getCityId();

		$helper = new WikiaHomePageHelper();
		$vizData = $helper->getWikiInfoForVisualization( $cityId, $wgLanguageCode );
		$stats = $helper->getWikiStats( $cityId );
		
		$this->setHeader( 'cityId',				$cityId );
		$this->setHeader( 'cityTitle',			WikiFactory::getVarValueByName( 'wgSitename', $cityId ) );
		$this->setHeader( 'cityUrl',			WikiFactory::getVarValueByName( 'wgServer', $cityId ) );
		$this->setHeader( 'cityArticlesNum',	$stats['articles'] );
		$this->setHeader( 'cityImagesNum',      $stats['images'] );
		$this->setHeader( 'cityVideosNum',      $stats['videos'] );
		$this->setHeader( 'hub',                $result['hub'] );
		$this->setHeader( 'image',              array_shift( $vizData['images'] ) );
		$this->setHeader( 'promoted',           $vizData['promoted'] );
		$this->setHeader( 'official',           $vizData['official'] );
		$this->setHeader( 'new',                $vizData['new'] );
		$this->setHeader( 'hot',                $vizData['hot'] );
		$this->setHeader( 'description',        $vizData['description'] );
	}
}