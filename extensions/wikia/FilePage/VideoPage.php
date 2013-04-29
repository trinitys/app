<?php

if( !defined( 'MEDIAWIKI' ) )
	die( 1 );

/**
 * Special handling for video description pages
 *
 * @ingroup Media
 */
class WikiaVideoPage extends WikiaImagePage {

	protected static $videoWidth = 670;

	protected function openShowImage(){
		global $wgOut, $wgRequest, $wgEnableVideoPageRedesign;
		wfProfileIn( __METHOD__ );
		$timestamp = $wgRequest->getInt('t', 0);

		if ( $timestamp > 0 ) {
			$img = wfFindFile( $this->mTitle, $timestamp );
			if ( !($img instanceof LocalFile && $img->exists()) ) {
				$img = $this->getDisplayedFile();
			}
		} else {
			$img = $this->getDisplayedFile();
		}

		F::build('JSMessages')->enqueuePackage('VideoPage', JSMessages::EXTERNAL);

		$app = F::app();
		$autoplay = $app->wg->VideoPageAutoPlay;

		//if ( empty($wgEnableVideoPageRedesign) ) {
			//$wgOut->addHTML( '<div class="fullImageLink" id="file">'.$img->getEmbedCode( self::$videoWidth, $autoplay ).$this->getVideoInfoLine().'</div>' );
		//} else {
			$imageLink = '<div class="fullImageLink" id="file">'.$img->getEmbedCode( self::$videoWidth, $autoplay ).'</div>';

			$wgOut->addHTML($imageLink);

			$caption = $this->getCaptionLine($img);

			$wgOut->addHTML($caption);
		//}

		wfProfileOut( __METHOD__ );
	}

	protected function getCaptionLine($img) {
		$app = F::app();

		$captionDetails = array(
			'expireDate' => $img->getExpirationDate(),
			'provider' => $img->getProviderName(),
			'providerUrl' => $img->getProviderHomeUrl(),
			'detailUrl' => $img->getProviderDetailUrl(),
			'views' => MediaQueryService::getTotalVideoViewsByTitle( $img->getTitle()->getDBKey() ),
		);

		$caption = $app->renderView( 'FilePageController', 'videoCaption', $captionDetails );

		return $caption;
	}

	protected function getVideoInfoLine() {
		global $wgWikiaVideoProviders;

		$img = $this->getDisplayedFile();
		$detailUrl = $img->getProviderDetailUrl();
		$provider = $img->getProviderName();
		if ( !empty($provider) ) {
			$providerName = explode( '/', $provider );
			$provider = array_pop( $providerName );
		}
		$providerUrl = $img->getProviderHomeUrl();

		$link = '<a href="' . $detailUrl . '" class="external" target="_blank">' . $this->mTitle->getText() . '</a>';
		$providerLink = '<a href="' . $providerUrl . '" class="external" target="_blank">' . $provider . '</a>';
		$s = '<div id="VideoPageInfo">' . wfMsgExt( 'videohandler-video-details', array('replaceafter'), $link, $providerLink )  . '</div>';
		return $s;
	}

	/* This is not being called anymore because the parent method was moved to WikFilePage class
	 * It used to be a fix for: https://wikia.fogbugz.com/default.asp?26737
	 * So it's currently broken places like this: http://glee.wikia.com/wiki/File:Glee_Sugar%27s_Audition-3x01
	 */
	public function getDuplicates() {

		wfProfileIn( __METHOD__ );
		$img =  $this->getDisplayedFile();
		$handler = $img->getHandler();
		if ( $handler instanceof VideoHandler && $handler->isBroken() ) {
			$res = $this->dupes = array();
		} else {
			$dupes = parent::getDuplicates();
			$finalDupes = array();
			foreach( $dupes as $dupe ) {
		                if ( WikiaFileHelper::isFileTypeVideo( $dupe ) && $dupe instanceof WikiaLocalFile ) {
		                    if ( $dupe->getProviderName() != $img->getProviderName() ) continue;
		                    if ( $dupe->getVideoId() != $img->getVideoId() ) continue;
		                    $finalDupes[] = $dupe;
		                }
			}
			$res = $finalDupes;
		}
		wfProfileOut( __METHOD__ );
		return $res;
	}

	// This is a helper function and can be moved out of here
	public static function getVideosCategory() {

		$cat = F::app()->wg->ContLang->getFormattedNsText( NS_CATEGORY );
		return ucfirst($cat) . ':' . wfMsgForContent( 'videohandler-category' );
	}

	public function getUploadUrl() {
		wfProfileIn( __METHOD__ );
		$this->loadFile();
		$uploadTitle = SpecialPage::getTitleFor( 'WikiaVideoAdd' );
		wfProfileOut( __METHOD__ );
		return $uploadTitle->getFullUrl( array(
			'name' => $this->getDisplayedFile()->getName()
		 ) );
	}
}
