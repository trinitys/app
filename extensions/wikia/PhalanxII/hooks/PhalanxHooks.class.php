<?php

class PhalanxHooks extends WikiaObject {
	function __construct() {
		parent::__construct();
		F::setInstance( __CLASS__, $this );
	}

	/**
	 * Add a link to central:Special:Phalanx from Special:Contributions/USERNAME
	 * if the user has 'phalanx' permission
	 * @param $id Integer: user ID
	 * @param $nt Title: user page title
	 * @param $links Array: tool links
	 * @return boolean true
	 */
	public function loadLinks( $id, $nt, &$links ) {
		$this->wf->profileIn( __METHOD__ );

		if ( $this->wg->User->isAllowed( 'phalanx' ) ) {
			$links[] = Linker::makeKnownLinkObj(
				GlobalTitle::newFromText( 'Phalanx', NS_SPECIAL, WikiFactory::COMMUNITY_CENTRAL ),
				'PhalanxBlock',
				wfArrayToCGI(
					array(
						'wpPhalanxTypeFilter[]' => '8',
						'wpPhalanxCheckBlocker' => $nt->getText()
					)
				)
			);
		}

		$this->wf->profileOut( __METHOD__ );
		return true;
	}

	/**
	 * Performs spam check for 3rd party extension. Third parameter will be provided with matching block data
	 *
	 * @param $text string content to check for spam
	 * @param $typeId int block type (see Phalanx::TYPE_* constants)
	 * @param $blockData array array to be provided with matching block details (pass as a reference)
	 * @return boolean spam check result
	 *
	 * @author macbre
	 */
	public function onSpamFilterCheck($text, $typeId, &$blockData) {
		$this->wf->profileIn( __METHOD__ );

		if ($text === '') {
			$this->wf->profileOut( __METHOD__ );
			return true;
		}

		$model = PhalanxModel::newFromType($typeId, $text);

		if (is_null($model)) {
			throw new WikiaException("Unsupported block type passed - #{$typeId}");
		}

		// get type ID -> type mapping
		$types = Phalanx::getAllTypeNames();
		$ret = $model->match($types[$typeId]);

		// pass matching block details
		if ( $ret === false ) {
			$blockData = (array) $model->getBlock();
			$this->wf->Debug( __METHOD__ . ": spam check blocked '{$text}'\n" );
		}

		$this->wf->profileOut( __METHOD__ );
		return $ret;
	}

	/**
	 * Add/edit Phalanx block
	 *
	 * @param $data Array contains block information, possible keys: id, author_id, text, type, timestamp, expire, exact, regex, case, reason, lang, ip_hex
	 * @return int id block or false if error
	 *
	 * @author moli
	 */
	public function onEditPhalanxBlock( &$data ) {
		$this->wf->profileIn( __METHOD__ );

		if ( !isset( $data['id'] ) ) {
			return false;
		}

		$phalanx = Phalanx::newFromId( $data['id'] );

		foreach ( $data as $key => $val ) {
			if ( $key == 'id' ) continue;

			$phalanx[ $key ] = $val;
		}

		$typemask = 0;
		if ( is_array( $phalanx['type'] ) ) {
			foreach ( $phalanx['type'] as $type ) {
				$typemask |= $type;
			}
		}

		$multitext = '';
		if ( isset( $phalanx['multitext'] ) && !empty( $phalanx['multitext'] ) ) {
			$multitext = $phalanx['multitext'];
		}

		unset( $phalanx['multitext'] );

		if ( ( empty( $phalanx['text'] ) && empty( $multitext ) ) || empty( $typemask ) ) {
			$this->wf->profileOut( __METHOD__ );
			return false;
		}

		$phalanx['type'] = $typemask;

		if ( $phalanx['lang'] == 'all' ) {
			$phalanx['lang'] = null;
		}

		if ( $phalanx['expire'] === '' ) {
			// don't change expire
			unset($phalanx['expire']);
		}
		else if ( $phalanx['expire'] != 'infinite' ) {
			$expire = strtotime( $phalanx['expire'] );
			if ( $expire < 0 || $expire === false ) {
				$this->wf->profileOut( __METHOD__ );
				return false;
			}
			$phalanx['expire'] = wfTimestamp( TS_MW, $expire );
		} else {
			$phalanx['expire'] = null ;
		}

		if ( empty( $multitext ) ) {
			/* single mode - insert/update record */
			$data['id'] = $phalanx->save();
			$result = $data['id'] ? array( "success" => array( $data['id'] ), "failed" => 0 ) : false;
		}
		else {
			/* non-empty bulk field */
			$bulkdata = explode( "\n", $multitext );
			if ( count($bulkdata) > 0 ) {
				$result = array( 'success' => array(), 'failed' => 0 );
				foreach ( $bulkdata as $bulkrow ) {
					$bulkrow = trim($bulkrow);
					$phalanx['id'] = null;
					$phalanx['text'] = $bulkrow;

					$data['id'] = $phalanx->save();
					if ( $data['id'] ) {
						$result[ 'success' ][] = $data['id'];
					} else {
						$result[ 'failed' ]++;
					}
				}
			} else {
				$result = false;
			}
		}

		if ( $result !== false ) {
			$service = new PhalanxService();
			$ret = $service->reload( $result["success"] );
		} else {
			$ret = $result;
		}

		$this->wf->profileOut( __METHOD__ );
		return $ret;
	}

	/**
	 * Delete Phalanx block
	 *
	 * @param $id Int - block ID
	 * @return boolean true or false if error
	 *
	 * @author moli
	 */
	public function onDeletePhalanxBlock( $id ) {
		$this->wf->profileIn( __METHOD__ );

		$phalanx = Phalanx::newFromId($id);

		$id = $phalanx->delete();
		if ( $id ) {
			$service = new PhalanxService();
			$ids = array( $id );
			$ret = $service->reload( $ids );
		} else {
			$ret = false;
		}

		$this->wf->profileOut( __METHOD__ );
		return $ret;
	}
}
