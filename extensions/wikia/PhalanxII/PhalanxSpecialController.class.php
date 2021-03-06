<?php

class PhalanxSpecialController extends WikiaSpecialPageController {

	const RESULT_BLOCK_ADDED = 1;
	const RESULT_BLOCK_UPDATED = 2;
	const RESULT_ERROR = 3;

	private $title;
	private $service;

	public function __construct() {
		parent::__construct('Phalanx', 'phalanx' /* restrictions */);
		$this->includable(false);

		$this->title = SpecialPage::getTitleFor('Phalanx');
		$this->service = new PhalanxService();
	}

	/**
	 * Special page main entry point
	 */
	public function index() {
		$this->wf->profileIn( __METHOD__ );

		$this->wg->Out->setPageTitle( $this->wf->Msg('phalanx-title') );
		if ( !$this->userCanExecute( $this->wg->User ) ) {
			$this->displayRestrictionError();
			$this->wf->profileOut( __METHOD__ );
			return;
		}

		// select the current tab
		switch($this->getPar()) {
			case 'test':
				$currentTab = 'test';
				break;

			default:
				$currentTab = 'main';
		}

		// load resource loader module
		$this->wg->Out->addModules('ext.wikia.Phalanx');
		$this->wg->Out->addJsConfigVars('wgPhalanxToken', $this->getToken());

		$this->forward('PhalanxSpecial', $currentTab);

		$this->wf->profileOut( __METHOD__ );
	}

	/**
	 * Renders first tab - blocks creation / edit
	 */
	public function main() {
		$this->wf->profileIn( __METHOD__ );

		// creating / editing a block
		if ( $this->wg->Request->wasPosted() ) {
			$res = $this->handleBlockPost();

			// add a message that will be shown after the redirect
			if ($res === self::RESULT_ERROR) {
				NotificationsController::addConfirmation(
					wfMsg('phalanx-block-failure'),
					NotificationsController::CONFIRMATION_ERROR
				);
			}
			else {
				NotificationsController::addConfirmation(
					wfMsg( $res === self::RESULT_BLOCK_ADDED ?  'phalanx-block-success' :  'phalanx-modify-success'),
					NotificationsController::CONFIRMATION_NOTIFY
				);
			}

			$this->wg->Out->redirect($this->title->getFullURL());

			$this->wf->profileOut( __METHOD__ );
			return;
		}

		/* set pager */
		$pager = new PhalanxPager();
		$listing  = $pager->getNavigationBar();
		$listing .= $pager->getBody();
		$listing .= $pager->getNavigationBar();

		$data = $this->blockDataForForm();
		$editMode = !empty($data['id']);

		$expiries = Phalanx::getExpireValues();
		if ($editMode) {
			$expiries = array_merge(array('' => wfMsg('phalanx-expiries-select')), $expiries);
		}

		$this->setVal( 'expiries', $expiries );
		$this->setVal( 'languages', $this->wg->PhalanxSupportedLanguages );
		$this->setVal( 'listing', $listing );
		$this->setVal( 'data',  $data);
		$this->setVal( 'editMode',  $editMode);
		$this->setVal( 'action', $this->title->getLocalURL() );
		$this->setVal( 'showEmail', $this->wg->User->isAllowed( 'phalanxemailblock' ) );
		$this->setVal( 'typeFilter', $pager->getSearchFilter() );
		$this->setVal( 'blockTypes', Phalanx::getAllTypeNames() );

		$this->wf->profileOut( __METHOD__ );
	}

	/**
	 * Renders second tab - blocks testing
	 */
	public function test() {
		$title = SpecialPage::getTitleFor('Phalanx', 'test');
		$this->setVal( 'action', $title->getLocalURL() );

		$blockText = $this->wg->Request->getText('wpBlockText');
		$this->setVal( 'blockText', $blockText );

		// check the text against all blocks
		if ($blockText !== '') {
			$this->setVal( 'listing', $this->handleBlockTest($blockText) );
		}
	}

	/**
	 * Renders navigation tabs on special page
	 */
	public function tabs() {
		$this->setVal('currentTab', $this->getVal('currentTab'));
		$this->setVal('phalanxMainTitle', $this->title);
		$this->setVal('phalanxTestTitle', SpecialPage::getTitleFor('Phalanx', 'test'));
	}

	/**
	 * Returns current block data
	 *
	 * @return array
	 */
	private function blockDataForForm() {
		$data = array();

		$id = $this->wg->Request->getInt( 'id' );
		if ( $id > 0 ) {
			// block edit
			$data = Phalanx::newFromId($id);
			$data['type'] = Phalanx::getTypeNames( $data['type'] );
			$data['checkId'] = $id;
			$data['checkBlocker'] = '';
			$data['typeFilter'] = array();
		}
		else {
			// block search
			$data['checkBlocker'] = $this->wg->Request->getText( 'wpPhalanxCheckBlocker' , '');
			$data['checkId'] = $this->wg->Request->getIntOrNull( 'id' );
			$data['type'] = $this->wg->Request->getArray( 'wpPhalanxType' );
			$data['typeFilter'] = $this->wg->Request->getArray( 'wpPhalanxTypeFilter' );
			$data['text'] = '';
			$data['lang'] = '';
			$data['expire'] = '';
			$data['reason'] = '';
		}

		return $data;
	}

	/**
	 * Handle POST request to edit / create a block
	 *
	 * @return int
	 */
	private function handleBlockPost() {
		$this->wf->profileIn( __METHOD__ );

		$id = $this->wg->Request->getInt( 'id', 0 );
		$isBlockUpdate = ($id !== 0);
		$data = array(
			'id'         => $id,
			'text'       => $this->wg->Request->getText( 'wpPhalanxFilter' ),
			'exact'      => $this->wg->Request->getCheck( 'wpPhalanxFormatExact' ) ? 1 : 0,
			'case'       => $this->wg->Request->getCheck( 'wpPhalanxFormatCase' ) ? 1 : 0,
			'regex'      => $this->wg->Request->getCheck( 'wpPhalanxFormatRegex' ) ? 1 : 0,
			'timestamp'  => wfTimestampNow(),
			'author_id'  => $this->wg->User->getId(),
			'reason'     => $this->wg->Request->getText( 'wpPhalanxReason' ),
			'lang'       => $this->wg->Request->getVal( 'wpPhalanxLanguages', null ),
			'type'       => $this->wg->Request->getArray( 'wpPhalanxType' ),
			'multitext'  => $this->wg->Request->getText( 'wpPhalanxFilterBulk' ),
			'expire'     => $this->wg->Request->getText('wpPhalanxExpire')
		);
		if ( !wfRunHooks( "EditPhalanxBlock", array( &$data ) ) ) {
			$ret = self::RESULT_ERROR;
		} else {
			$ret = $isBlockUpdate ? self::RESULT_BLOCK_UPDATED : self::RESULT_BLOCK_ADDED;
		}

		$this->wf->profileOut( __METHOD__ );
		return $ret;
	}

	/**
	 * Get HTML with the list of block that apply for given text
	 *
	 * @param $blockText string text to be test against all blocks
	 * @return string HTML
	 */
	private function handleBlockTest($blockText) {
		$this->wf->profileIn( __METHOD__ );

		$service = new PhalanxService();
		$service->setLimit(20);

		$listing = '';
		$noMatches = true;

		foreach(Phalanx::getAllTypeNames() as $blockType) {
			$res = $service->match($blockType, $blockText);

			if (empty($res)) {
				continue;
			}

			$noMatches = false;

			$pager = new PhalanxBlockTestPager($blockType);
			$pager->setRows($res);
			$listing .= $pager->getHeader();
			$listing .= $pager->getBody();
		}

		if ($noMatches) {
			$pager = new PhalanxBlockTestPager(0);
			$listing .= $pager->getEmptyBody();
		}

		$this->wf->profileOut( __METHOD__ );
		return $listing;
	}

	/**
	 * Method called via AJAX from Special:Phalanx to remove blocks
	 */
	public function unblock() {
		$this->wf->profileIn( __METHOD__ );

		$this->response->setFormat('json');
		$this->setVal('success', false);

		if ( !$this->userCanExecute( $this->wg->User ) ) {
			$this->setVal('error', 'permission');

			$this->wf->profileOut( __METHOD__ );
			return;
		}

		$id = $this->request->getInt( 'blockId' );
		$token = $this->request->getVal( 'token' );

		// validate input
		if (!$id) {
			$this->setVal('error', 'id');

			$this->wf->profileOut( __METHOD__ );
			return;
		}

		if ($token != $this->getToken()) {
			$this->setVal('error', 'token');

			$this->wf->profileOut( __METHOD__ );
			return;
		}

		// delete a block
		if ( !wfRunHooks( "DeletePhalanxBlock", array( $id ) ) ) {
			$result = false;
		} else {
			$result = true;
		}

		$this->setVal('success', $result !== false);
		$this->wf->profileOut( __METHOD__ );
	}

	/**
	 * Method called via AJAX from Special:Phalanx to validate regexp
	 */
	public function validate() {
		$this->wf->profileIn( __METHOD__ );

		$this->response->setFormat('json');
		$this->setVal( 'valid', false);

		$regexp = $this->request->getVal( 'regexp' );
		$token = $this->request->getVal( 'token' );

		if ( !$this->userCanExecute( $this->wg->User ) ) {
			$this->setVal('error', 'permission');

			$this->wf->profileOut( __METHOD__ );
			return;
		}

		if ( $token == $this->getToken() ) {
			$this->setVal( 'valid', $this->service->validate( $regexp ) );
		}

		$this->wf->profileOut( __METHOD__ );
	}

	public function matchBlock() {
		$result = array();
		$token = $this->request->getVal( 'token' );
		$block = $this->request->getVal( 'block' );

		if ( $token == $this->getToken() ) {
			foreach ( Phalanx::getAllTypeNames() as $type => $typeName ) {
				$blocks = $this->service->match( $type, $block );
				if ( !empty( $blocks ) ) {
					$result[$type] = $blocks;
				}
			}
		}
		$this->setVal('blocks', $result);
	}

	private function getToken() {
		return $this->wg->User->getEditToken();
	}
}
