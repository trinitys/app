<?php

class PhalanxPager extends ReverseChronologicalPager {
	protected $app = null;
	private $id = 0;
	private $pInx = '';

	public function __construct() {
		parent::__construct();
		$this->app = F::app();

		$this->mDb = wfGetDB( DB_SLAVE, array(), $this->app->wg->ExternalSharedDB );
		$this->mSearchText = $this->app->wg->Request->getText( 'wpPhalanxCheckBlocker', null );
		$this->mSearchFilter = $this->app->wg->Request->getArray( 'wpPhalanxTypeFilter' );
		$this->mSearchId = $this->app->wg->Request->getInt( 'id' );

		// handle "type" parameter from URLs comming from hook messages
		$type = $this->app->wg->Request->getInt('type');
		if ($type > 0) {
			$this->mSearchFilter = array($type);
		}

		$this->mTitle = F::build( 'Title', array( 'Phalanx/stats', NS_SPECIAL ), 'newFromText' );
		$this->mSkin = RequestContext::getMain()->getSkin();

		$this->phalanxPage = SpecialPage::getTitleFor('Phalanx');
		$this->phalanxStatsPage = SpecialPage::getTitleFor('PhalanxStats');
	}

	/**
	 * Get types filter as key/value collection:
	 *
	 * array(
	 *  typeId => true
	 * )
	 *
	 * @return Array
	 */
	function getSearchFilter() {
		if (is_array($this->mSearchFilter)) {
			$filters = array_map('intval', $this->mSearchFilter);
			return array_fill_keys($filters, true);
		}
		else {
			return array();
		}
	}

	function getQueryInfo() {
		$query['tables'] = 'phalanx';
		$query['fields'] = '*';

		if ( $this->mSearchId ) {
			$query['conds'][] = "p_id = {$this->mSearchId}";
		} else {
			if ( !empty( $this->mSearchText ) ) {
				$query['conds'][] = '(p_text like "%' . $this->mDb->escapeLike( $this->mSearchText ) . '%")';
			}

			if ( !empty( $this->mSearchFilter ) ) {
				$typemask = 0;
				foreach ( $this->mSearchFilter as $type ) {
					$typemask |= $type;
				}

				$query['conds'][] = "p_type & $typemask <> 0";
			}
		}

		return $query;
	}

	function getIndexField() {
		return 'p_timestamp';
	}

	function getStartBody() {
		return Html::openElement( 'ul', array( "id" => 'phalanx-block-' . $this->pInx . '-' . $this->id . '-stats' ) );
	}

	function getEndBody() {
		return Html::closeElement( 'ul' );
	}

	function getEmptyBody() {
		return Html::element('div', array('class' => 'error'), wfMsg('phalanx-no-results'));
	}

	function formatRow( $row ) {
		// hide e-mail filters
		if ( ( $row->p_type & Phalanx::TYPE_EMAIL ) && !$this->app->wg->User->isAllowed( 'phalanxemailblock' ) ) {
			return '';
		}

		if (isset($row->p_author_id)) {
			$author = F::build('User', array( $row->p_author_id ), 'newFromId');
			$authorName = $author->getName();
		}
		else {
			$authorName = '';
		}

		$statsUrl = sprintf( "%s/%s", $this->phalanxStatsPage->getLocalUrl(), $row->p_id );

		$html  = Html::openElement( 'li', array( 'id' => 'phalanx-block-' . $row->p_id ) );
		$html .= Html::element( 'b', array('class' => 'blockContent'), htmlspecialchars( $row->p_text ) );
		$html .= sprintf( " (%s%s%s) ",
			( !empty($row->p_regex) ? 'regex' : 'plain' ),
			( !empty($row->p_case)  ? ',case' : '' ),
			( !empty($row->p_exact) ? ',exact': '' )
		);

		/* control links */
		$html .= sprintf( " &bull; %s &bull; %s",
			Html::element( 'a', array(
				'class' => 'modify',
				'href' => $this->phalanxPage->getLocalUrl( array( 'id' => $row->p_id ) )
			), $this->app->wf->Msg('phalanx-link-modify') ),
			Html::element( 'a', array(
				'class' => 'stats',
				'href' => $statsUrl
			), $this->app->wf->Msg('phalanx-link-stats') )
		);

		/* remove block button - handled via AJAX */
		$html .= Html::element( 'button', array(
			'class' => 'unblock',
			'data-id' => $row->p_id,
		), $this->app->wf->Msg('phalanx-link-unblock') );

		$html .= Html::element('br');

		/* types */
		$html .= $this->app->wf->Msg('phalanx-display-row-blocks', implode( ', ', Phalanx::getTypeNames( $row->p_type ) ) );

		/* created */
		if (isset($row->p_timestamp)) {
			$html .= sprintf( " &bull; %s ",
				$this->app->wf->MsgExt( 'phalanx-display-row-created', array('parseinline'),
					$authorName,
					$this->app->wg->Lang->timeanddate( $row->p_timestamp )
				)
			);
		}

		/* valid till */
		if (property_exists($row, 'p_expire')) {
			if (is_null($row->p_expire)) {
				$html .= sprintf( " &bull; %s ", $this->app->wf->Msg('phalanx-display-row-expire-infinity'));
			}
			else if (is_numeric($row->p_expire)) {
				$html .= sprintf( " &bull; %s ",
					$this->app->wf->Msg( 'phalanx-display-row-expire', $this->app->wg->Lang->timeanddate( $row->p_expire ))
				);
			}
		}

		$html .= Html::closeElement( "li" );

		return $html;
	}
}
