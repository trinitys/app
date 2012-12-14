<?php
class TrayController extends WikiaController {

	public function index() {
		$this->profileAvatar = AvatarService::renderAvatar($this->wg->User->getName(), 24);

		$this->response->addAsset([
			'extensions/wikia/hacks/Tray/css/TrayOverride.scss',
			'extensions/wikia/hacks/Tray/css/Tray.scss',
		]);
		$this->response->addAsset( 'extensions/wikia/hacks/Tray/js/Tray.js' );
	}

	/*
	public function outputHook( OutputPage &$out, &$text ) {
		$text .= $this->sendRequest( 'Tray', 'index' )->render();

		return $out;
	}
	*/

}
