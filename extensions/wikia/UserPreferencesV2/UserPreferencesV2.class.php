<?php
class UserPreferencesV2 {
	const MASTHEAD_OPTIONS_STORAGE_ARRAY_KEY_NAME = 'mastheadOptions';
	const MY_TOOLBAR_OPTIONS_STORAGE_ARRAY_KEY_NAME = 'myToolbarOptions';

	/**
	 * @const Name of property saved in user_properties table
	 */
	const LANDING_PAGE_PROP_NAME = 'userlandingpage';
	const LANDING_PAGE_MAIN_PAGE = 1;
	const LANDING_PAGE_WIKI_ACTIVITY = 2;
	const LANDING_PAGE_RECENT_CHANGES = 3;

	/**
	 * @brief This function change user preferences special page
	 *
	 * @param User $user reference to the current user
	 * @param array $defaultPreferences reference to the default preferences array
	 *
	 * @return Bool
	 */
	public function onGetPreferences($user, &$defaultPreferences) {
		global $wgEnableWallExt, $wgOut, $wgScriptPath, $wgServer, $wgUser, $wgAuth;

		//add javascript
		// TODO: use $wgExtensionsPath instead
		$wgOut->addScriptFile($wgScriptPath . '/extensions/wikia/UserPreferencesV2/js/UserPreferencesV2.js');

		// remove Appearance tab (custom css/js)
		unset( $defaultPreferences['commoncssjs'] );

		//Tab 1: User Profile
		unset($defaultPreferences['userid']);
		unset($defaultPreferences['editcount']);
		unset($defaultPreferences['registrationdate']);
		unset($defaultPreferences['realname']);
		unset($defaultPreferences['rememberpassword']);
		unset($defaultPreferences['ccmeonemails']);
		if (isset($defaultPreferences['username'])) {
			$defaultPreferences['username']['label-message'] = 'preferences-v2-username';
		}
		if (isset($defaultPreferences['usergroups'])) {
			$defaultPreferences['usergroups']['label-message'] = 'preferences-v2-usergroups';
		}
		if (isset($defaultPreferences['gender'])) {
			$defaultPreferences['gender']['label-message'] = 'preferences-v2-gender';
			$defaultPreferences['gender']['help-message'] = '';
		}

		if (isset($defaultPreferences['password'])) {
			$defaultPreferences['password']['label-message'] = 'preferences-v2-password';
		}
		if (isset($defaultPreferences['oldsig'])) {
			$defaultPreferences['oldsig']['label-message'] = 'preferences-v2-oldsig';
		}
		if (isset($defaultPreferences['nickname'])) {
			$defaultPreferences['nickname']['label-message'] = 'preferences-v2-nickname';
		}
		if (isset($defaultPreferences['fancysig'])) {
			$defaultPreferences['fancysig']['label-message'] = 'preferences-v2-fancysig';
			$defaultPreferences['fancysig']['help-message'] = '';
		}
		if (isset($defaultPreferences['language'])) {
			$defaultPreferences['language']['section'] = 'personal/appearance';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'language');
		}
		if (isset($defaultPreferences['date'])) {
			$defaultPreferences['date']['section'] = 'personal/appearance';
			$defaultPreferences['date']['type'] = 'select';
			$defaultPreferences['date']['label-message'] = 'preferences-v2-date';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'date');
		}
		if (isset($defaultPreferences['timecorrection'])) {
			$defaultPreferences['timecorrection']['section'] = 'personal/appearance';
			$defaultPreferences['timecorrection']['label-message'] = 'preferences-v2-time';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'timecorrection');
		}

		if (isset($defaultPreferences['skin'])) {
			$defaultPreferences['skin']['section'] = 'personal/appearance';
			$defaultPreferences['skin']['type'] = 'select';
			$defaultPreferences['skin']['label-message'] = 'preferences-v2-skin';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'skin');
		}
		if (isset($defaultPreferences[self::LANDING_PAGE_PROP_NAME])) {
			$redirectOptions[wfMsg('preferences-v2-redirect-main-page')] = self::LANDING_PAGE_MAIN_PAGE;
			$redirectOptions[wfMsg('preferences-v2-redirect-wiki-activity')] = self::LANDING_PAGE_WIKI_ACTIVITY;
			$redirectOptions[wfMsg('preferences-v2-redirect-recent-changes')] = self::LANDING_PAGE_RECENT_CHANGES;
			$defaultPreferences[self::LANDING_PAGE_PROP_NAME]['type'] = 'select';
			$defaultPreferences[self::LANDING_PAGE_PROP_NAME]['options'] = $redirectOptions;
			$defaultPreferences[self::LANDING_PAGE_PROP_NAME]['label-message'] = 'preferences-v2-user-landing-page';
			$defaultPreferences[self::LANDING_PAGE_PROP_NAME]['section'] = 'personal/appearance';
			$defaultPreferences[self::LANDING_PAGE_PROP_NAME]['help'] = wfMsg('preferences-v2-redirect-explanation', parse_url( $wgServer, PHP_URL_HOST ) );
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, self::LANDING_PAGE_PROP_NAME);
		}
		if (isset($defaultPreferences['showAds'])) {
			$defaultPreferences['showAds']['section'] = 'personal/appearance';
			$defaultPreferences['showAds']['label-message'] = 'tog-showAdsv2';
			$defaultPreferences['showAds']['type'] = 'select';
			$adOptions[wfMsg('preferences-v2-showads-disable')] = 0;
			$adOptions[wfMsg('preferences-v2-showads-enable')] = 1;
			$defaultPreferences['showAds']['options'] = $adOptions;
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'showAds');
		}

		//Tab 2: Email
		unset($defaultPreferences['imagesize']);
		unset($defaultPreferences['thumbsize']);
		unset($defaultPreferences['math']);
		if (isset($defaultPreferences['emailaddress'])) {
			$defaultPreferences['emailaddress']['type'] = $wgAuth->allowPropChange( 'emailaddress' ) ? 'email' : 'info' ;
			$defaultPreferences['emailaddress']['default'] = $user->getEmail() ? htmlspecialchars( $user->getEmail() ) : '' ;
			$defaultPreferences['emailaddress']['section'] = 'emailv2/addressv2';
			$defaultPreferences['emailaddress']['label-message'] = 'preferences-v2-my-email-address';
		}
		if (isset($defaultPreferences['emailauthentication'])) {
			$defaultPreferences['emailauthentication']['section'] = 'emailv2/addressv2';
		}

		if (isset($defaultPreferences['watchdefault'])) {
			$defaultPreferences['watchdefault']['section'] = 'emailv2/followed-pages-iv2';
			$defaultPreferences['watchdefault']['label-message'] = 'preferences-v2-watchdefault';
		}
		if (isset($defaultPreferences['watchmoves'])) {
			$defaultPreferences['watchmoves']['section'] = 'emailv2/followed-pages-iv2';
			$defaultPreferences['watchmoves']['label-message'] = 'preferences-v2-watchmoves';
		}
		if (isset($defaultPreferences['watchdeletion'])) {
			if (in_array("autoconfirmed", $wgUser->getEffectiveGroups()) || $wgUser->isEmailConfirmed()) {
				$defaultPreferences['watchdeletion']['section'] = 'emailv2/followed-pages-iv2';
				$defaultPreferences['watchdeletion']['label-message'] = 'preferences-v2-watchdeletion';
				$defaultPreferences['watchdeletion']['type'] = 'toggle';
			}
		}
		if (isset($defaultPreferences['watchcreations'])) {
			$defaultPreferences['watchcreations']['section'] = 'emailv2/followed-pages-iv2';
			$defaultPreferences['watchcreations']['label-message'] = 'preferences-v2-watchcreations';
		}

		if (isset($defaultPreferences['enotifwatchlistpages'])) {
			$defaultPreferences['enotifwatchlistpages']['section'] = 'emailv2/email-me-v2';
			$defaultPreferences['enotifwatchlistpages']['label-message'] = 'tog-enotifwatchlistpages-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'enotifwatchlistpages');
		}
		if (isset($defaultPreferences['enotifminoredits'])) {
			$defaultPreferences['enotifminoredits']['section'] = 'emailv2/email-me-v2';
			$defaultPreferences['enotifminoredits']['label-message'] = 'tog-enotifminoredits-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'enotifminoredits');
		}
		if (isset($defaultPreferences['enotifusertalkpages'])) {
			$defaultPreferences['enotifusertalkpages']['section'] = 'emailv2/email-me-v2';
			$defaultPreferences['enotifusertalkpages']['label-message'] = 'tog-enotifusertalkpages-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'enotifusertalkpages');
		}
		if (isset($defaultPreferences['marketingallowed'])) {
			$defaultPreferences['marketingallowed']['section'] = 'emailv2/email-me-v2';
			$defaultPreferences['marketingallowed']['label-message'] = 'tog-marketingallowed-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'marketingallowed');
		}
		if (isset($defaultPreferences['watchlistdigest'])) {
			$defaultPreferences['watchlistdigest']['section'] = 'emailv2/email-me-v2';
			$defaultPreferences['watchlistdigest']['label-message'] = 'tog-watchlistdigest-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'watchlistdigest');
		}
		if (isset($defaultPreferences['marketingallowed'])) {
			$defaultPreferences['marketingallowed']['section'] = 'emailv2/email-me-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'marketingallowed');
		}
		if ($wgEnableWallExt) {
			if (isset($defaultPreferences['enotifwallthread'])) {
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'enotifwallthread');
			}
			if (isset($defaultPreferences['enotifmywall'])) {
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'enotifmywall');
			}
		}

		if (isset($defaultPreferences['htmlemails'])) {
			$defaultPreferences['htmlemails']['section'] = 'emailv2/email-advanced-v2';
			$defaultPreferences['htmlemails']['label-message'] = 'tog-htmlemails-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'htmlemails');
		}
		if (isset($defaultPreferences['disablemail'])) {
			$defaultPreferences['disablemail']['section'] = 'emailv2/email-advanced-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'disablemail');
		}
		if (isset($defaultPreferences['enotifrevealaddr'])) {
			$defaultPreferences['enotifrevealaddr']['section'] = 'emailv2/email-advanced-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'enotifrevealaddr');
		}
		if (array_key_exists('watchlistdigestclear', $defaultPreferences)) {
			$defaultPreferences['watchlistdigestclear']['section'] = 'emailv2/email-advanced-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'watchlistdigestclear');
		}

		if (isset($defaultPreferences['unsubscribed'])) {
			$defaultPreferences['unsubscribed']['section'] = 'emailv2/email-unsubscribe';
			$defaultPreferences['unsubscribed']['label-message'] = 'unsubscribe-preferences-toggle-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'unsubscribed');
		}

		//Tab 3: Editing
		if (isset($defaultPreferences['watchlistdays'])) {
			$defaultPreferences['watchlistdays']['help'] = '';
		}
		if (isset($defaultPreferences['wllimit'])) {
			$defaultPreferences['wllimit']['help'] = '';
		}
		unset($defaultPreferences['nowserver']);
		unset($defaultPreferences['nowlocal']);
		unset($defaultPreferences['underline']);
		unset($defaultPreferences['stubthreshold']);
		unset($defaultPreferences['highlightbroken']);
		unset($defaultPreferences['toggle']);
		unset($defaultPreferences['showtoc']);
		unset($defaultPreferences['nocache']);
		unset($defaultPreferences['showjumplinks']);
		unset($defaultPreferences['numberheadings']);
		if (isset($defaultPreferences['enablerichtext'])) {
			$defaultPreferences['enablerichtext']['section'] = 'editing/editing-experience';
		}
		if (isset($defaultPreferences['disablelinksuggest'])) {
			$defaultPreferences['disablelinksuggest']['section'] = 'editing/editing-experience';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'disablelinksuggest');
		}
		if ($user->mOptions['skin'] == 'monobook') {
			if (isset($defaultPreferences['showtoolbar'])) {
				$defaultPreferences['showtoolbar']['section'] = 'editing/monobookv2';
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'showtoolbar');
			}
			if (isset($defaultPreferences['previewontop'])) {
				$defaultPreferences['previewontop']['section'] = 'editing/monobookv2';
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'previewontop');
			}
			if (isset($defaultPreferences['previewonfirst'])) {
				$defaultPreferences['previewonfirst']['section'] = 'editing/monobookv2';
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'previewonfirst');
			}
			if (isset($defaultPreferences['cols'])) {
				$defaultPreferences['cols']['section'] = 'editing/monobookv2';
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'cols');
			}
			if (isset($defaultPreferences['rows'])) {
				$defaultPreferences['rows']['section'] = 'editing/monobookv2';
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'rows');
			}
		}
		if (isset($defaultPreferences['editsectiononrightclick'])) {
			$defaultPreferences['editsectiononrightclick']['label-message'] = 'tog-editsectiononrightclick-v2';
		}
		if (isset($defaultPreferences['editondblclick'])) {
			$defaultPreferences['editondblclick']['label-message'] = 'tog-editondblclick-v2';
		}
		if (isset($defaultPreferences['disablecategoryselect'])) {
			$defaultPreferences['disablecategoryselect']['section'] = 'editing/starting-an-edit';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'disablecategoryselect');
		}

		//Tab 4: Under the Hood
		if (isset($defaultPreferences['rcdays'])) {
			$defaultPreferences['rcdays']['section'] = 'under-the-hood/recent-changesv2';
			$defaultPreferences['rcdays']['help'] = '';
		}
		if (isset($defaultPreferences['rclimit'])) {
			$defaultPreferences['rclimit']['section'] = 'under-the-hood/recent-changesv2';
			$defaultPreferences['rclimit']['help'] = '';
			$defaultPreferences['rclimit']['help-message'] = '';
		}
		if (isset($defaultPreferences['usenewrc'])) {
			$defaultPreferences['usenewrc']['section'] = 'under-the-hood/recent-changesv2';
		}
		if (isset($defaultPreferences['hideminor'])) {
			$defaultPreferences['hideminor']['section'] = 'under-the-hood/recent-changesv2';
		}
		if (isset($defaultPreferences['watchlistdays'])) {
			$defaultPreferences['watchlistdays']['section'] = 'under-the-hood/followed-pagesv2';
			$defaultPreferences['watchlistdays']['help'] = '';
		}
		if (isset($defaultPreferences['wllimit'])) {
			$defaultPreferences['wllimit']['section'] = 'under-the-hood/followed-pagesv2';
			$defaultPreferences['wllimit']['help'] = '';
		}
		if (isset($defaultPreferences['extendwatchlist'])) {
			$defaultPreferences['extendwatchlist']['section'] = 'under-the-hood/followed-pagesv2';
		}
		if (isset($defaultPreferences['watchlisthideminor'])) {
			$defaultPreferences['watchlisthideminor']['section'] = 'under-the-hood/followed-pagesv2';
		}
		if (isset($defaultPreferences['watchlisthidebots'])) {
			$defaultPreferences['watchlisthidebots']['section'] = 'under-the-hood/followed-pagesv2';
		}
		if (isset($defaultPreferences['watchlisthideown'])) {
			$defaultPreferences['watchlisthideown']['section'] = 'under-the-hood/followed-pagesv2';
		}
		if (isset($defaultPreferences['watchlisthideanons'])) {
			$defaultPreferences['watchlisthideanons']['section'] = 'under-the-hood/followed-pagesv2';
		}
		if (isset($defaultPreferences['watchlisthideliu'])) {
			$defaultPreferences['watchlisthideliu']['section'] = 'under-the-hood/followed-pagesv2';
		}
		if (isset($defaultPreferences['watchlisttoken'])) {
			$defaultPreferences['watchlisttoken']['section'] = 'under-the-hood/followed-pagesv2';
		}

		if (isset($defaultPreferences['highlightbroken'])) {
			$defaultPreferences['highlightbroken']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['highlightbroken']['type'] = 'toggle';
			$defaultPreferences['highlightbroken']['label-message'] = 'tog-highlightbrokenv2';
		}
		if (isset($defaultPreferences['showtoc'])) {
			$defaultPreferences['showtoc']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['showtoc']['type'] = 'toggle';
			$defaultPreferences['showtoc']['label-message'] = 'tog-showtoc';
		}
		if (isset($defaultPreferences['nocache'])) {
			$defaultPreferences['nocache']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['nocache']['type'] = 'toggle';
			$defaultPreferences['nocache']['label-message'] = 'tog-nocache';
		}
		if (isset($defaultPreferences['showhiddencats'])) {
			$defaultPreferences['showhiddencats']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['showhiddencats']['type'] = 'toggle';
			$defaultPreferences['showhiddencats']['label-message'] = 'tog-showhiddencats';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'showhiddencats');
		}
		if (isset($defaultPreferences['showjumplinks'])) {
			$defaultPreferences['showjumplinks']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['showjumplinks']['type'] = 'toggle';
			$defaultPreferences['showjumplinks']['label-message'] = 'tog-showjumplinks';
		}
		if (isset($defaultPreferences['justify'])) {
			$defaultPreferences['justify']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['justify']['type'] = 'toggle';
			$defaultPreferences['justify']['label-message'] = 'tog-justify';
		}
		if (isset($defaultPreferences['numberheadings'])) {
			$defaultPreferences['numberheadings']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['numberheadings']['type'] = 'toggle';
			$defaultPreferences['numberheadings']['label-message'] = 'tog-numberheadings';
		}
		if (isset($defaultPreferences['diffonly'])) {
			$defaultPreferences['diffonly']['section'] = 'under-the-hood/advanced-displayv2';
		}
		if (isset($defaultPreferences['norollbackdiff'])) {
			$defaultPreferences['norollbackdiff']['section'] = 'under-the-hood/advanced-displayv2';
		}
		if (isset($defaultPreferences['hidefollowedpages'])) {
			$defaultPreferences['hidefollowedpages']['section'] = 'under-the-hood/advanced-displayv2';
			$defaultPreferences['hidefollowedpages']['label-message'] = 'tog-hidefollowedpages-v2';
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'hidefollowedpages');
		}
		if (isset($defaultPreferences['justify'])) {
			if ($user->mOptions['skin'] == 'monobook') {
				$defaultPreferences['justify']['section'] = 'under-the-hood/advanced-displayv2';
				$defaultPreferences['justify']['label-message'] = 'tog-justify-v2';
				$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'justify');
			}
			else {
				unset($defaultPreferences['justify']);
			}
		}
		if (($wgEnableWallExt) && (isset($defaultPreferences['wallshowsource']))) {
			$defaultPreferences = $this->moveToEndOfArray($defaultPreferences, 'wallshowsource');
		}
		if (isset($defaultPreferences['hidepatrolled'])) {
			$defaultPreferences['hidepatrolled']['section'] = 'under-the-hood/patrolled-editsv2';
		}
		if (isset($defaultPreferences['newpageshidepatrolled'])) {
			$defaultPreferences['newpageshidepatrolled']['section'] = 'under-the-hood/patrolled-editsv2';
		}
		if (isset($defaultPreferences['watchlisthidepatrolled'])) {
			$defaultPreferences['watchlisthidepatrolled']['section'] = 'under-the-hood/patrolled-editsv2';
		}
		unset($defaultPreferences['enotiffollowedpages']);
		unset($defaultPreferences['enotiffollowedminoredits']);
		unset($defaultPreferences['nocache']);
		unset($defaultPreferences['numberheadings']);
		unset($defaultPreferences['showjumplinks']);

		return true;
	}

	/**
	 * @desc Sets default landing page property for users who don't have this property set in user_properties
	 *
	 * @param $defOpt
	 *
	 * @return bool true
	 */
	public function onUserGetDefaultOptions(&$defOpt) {
		$defOpt[self::LANDING_PAGE_PROP_NAME] = self::LANDING_PAGE_MAIN_PAGE;

		return true;
	}

	/**
	 * Before resetting the options, save the masthead info so we can restore it in onSpecialPreferencesAfterResetUserOptions
	 *
	 * @param $storage - storage in which we can save some options, it will be passed in onSpecialPreferencesAfterResetUserOptions hook call
	 * @param User $user
	 */
	public function onSpecialPreferencesBeforeResetUserOptions($preferences, &$user, &$storage) {
		//user identity box/masthead
		$userIdentityObject = new UserIdentityBox(F::app(), $user, 0);
		$mastheadOptions = $userIdentityObject->getFullData();
		$masthead = F::build('Masthead', array($user));
		if(!empty($masthead->mUser->mOptionOverrides['avatar'])) {
			$mastheadOptions['avatar'] = $masthead->mUser->mOptionOverrides['avatar'];
		}
		$storage[self::MASTHEAD_OPTIONS_STORAGE_ARRAY_KEY_NAME] = $mastheadOptions;

		//customize toolbar/myToolbar
		$skinName = RequestContext::getMain()->getSkin()->getSkinName();
		$oasisToolbarService = new OasisToolbarService($skinName);
		$toolbarNameInUserOptions = $oasisToolbarService->getToolbarOptionName();
		$toolbarCurrentList = $user->getOption($oasisToolbarService->getToolbarOptionName());
		$storage[self::MY_TOOLBAR_OPTIONS_STORAGE_ARRAY_KEY_NAME] = array(
			$toolbarNameInUserOptions => $toolbarCurrentList,
		);

		return true;
	}

	/**
	 * Restore some user options after reset
	 *
	 * @param $storage - storage with info from SpecialPreferencesBeforeResetUserOptions hook call
	 */
	public function onSpecialPreferencesAfterResetUserOptions($preferences, &$user, &$storage) {
		//user identity box/masthead
		$this->setUserOptionByNameAndValue($user, $storage[self::MASTHEAD_OPTIONS_STORAGE_ARRAY_KEY_NAME]);

		//customize toolbar
		$this->setUserOptionByNameAndValue($user, $storage[self::MY_TOOLBAR_OPTIONS_STORAGE_ARRAY_KEY_NAME]);

		return true;
	}

	// check if email is valid
	public static function onSavePreferences( &$formData, &$error ) {
		if ( array_key_exists('emailaddress', $formData) && !Sanitizer::validateEmail($formData['emailaddress']) ) {
			$error = F::app()->wf->Msg( 'invalidemailaddress' );
			return false;
		}

		return true;
	}

	public function onPreferencesTrySetUserEmail( $user, $newEmail, &$result ) {
		list( $status, $info ) = Preferences::trySetUserEmail( $user, $newEmail );

		/* @var $status Status */
		if ( $status instanceof Status && !$status->isGood() ) {
			$result = $status->getWikiText( $info );
			return false;
		}

		return true;
	}

	public function moveToEndOfArray($array, $key) {
		$temp[$key] = $array[$key];
		unset($array[$key]);
		return array_merge($array, $temp);
	}

	/**
	 * @param User $user
	 * @param $options
	 */
	protected function setUserOptionByNameAndValue($user, $options) {
		foreach($options as $optionName => $optionValue) {
			if(!is_array($optionValue)) {
				$user->setOption($optionName, $optionValue);
			}
		}
	}
}
