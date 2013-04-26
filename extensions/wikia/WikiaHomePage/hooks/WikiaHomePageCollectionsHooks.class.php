<?php
/**
 * Class WikiaHomePageCollectionsHooks
 * 
 * @desc Temporary class which will be removed after we create admin tool to manage collection and we resign from keeping them in WF variable
 */
$dir = dirname(__FILE__);
require_once( $dir . '/../CityVisualization.class.php' );

F::app()->registerHook('WikiFactoryChanged', 'WikiaHomePageCollectionsHooks', 'onWikiFactoryVarChanged');

class WikiaHomePageCollectionsHooks {
	const COLLECTIONS_LIST_WF_VAR_NAME = 'wgWikiaHomePageCollectionsWikis';
	
	public function onWikiFactoryVarChanged($cv_name, $city_id, $value) {
		$app = F::app();
		
		if( $this->isCollectionList($city_id, $cv_name) ) {
			Wikia::log(__METHOD__, '', 'Updating collection list cache after change');
			
			$visualization = new CityVisualization();
			foreach($value as $collectionId => $collection) {
				$app->wg->Memc->set($visualization->getCollectionCacheKey($collectionId), null);
				Wikia::log(__METHOD__, '', 'Purged memcached for collection #' . $collectionId);
			}
		}

		return true;
	}

	private function isCollectionList($city_id, $cv_name) {
		return $cv_name === self::COLLECTIONS_LIST_WF_VAR_NAME;
	}
}