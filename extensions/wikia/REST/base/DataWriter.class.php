<?php
namespace REST\base;

abstract class DataWriter {
	protected $content;
	protected $contentType;
	protected $charset;

	function __construct() {
		$this->content = null;
		$this->contentType = null;
		$this->charset = null;
	}

	function setContent( $content ) {
		$this->content = $content;
	}

	function getContent() {
		return $this->content;
	}

	function setContentType( $type ) {
		if ( !empty( $type ) && is_string( $type ) ) {
			$this->contentType = $type;
		} else {
			throw new \Exception( 'Cannot set content type to empty or non-string values' );
		}
	}

	function getContentType() {
		return $this->contentType;
	}

	function setCharset( $charset ) {
		if ( !empty( $charset ) && is_string( $charset ) ) {
			$this->charset = $charset;
		} else {
			throw new \Exception( 'Cannot set charset to empty or non-string values' );
		}
	}

	function getCharset() {
		return $this->charset;
	}

	abstract function toString();
}