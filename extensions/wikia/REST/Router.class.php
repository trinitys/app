<?php
namespace REST;

final class Router {
	protected $route;
	protected $reader;
	protected $writer;

	function __construct() {
		$this->route = null;
		$this->data = null;
		$this->writer = null;
	}

	public function run() {
		if ( !is_null( $this->route ) ) {
			$result = $this->route->resolve( $this->reader->getValues() );
			$this->writer->setContent( $result );

			if ( in_array( $this->route->getAction(), array( 'create', 'update', 'delete' ) ) ) {
				$factory = wfGetLBFactory();

				//commits only if writes were done on connection
				if ( $factory instanceof LBFactory ) {
					$factory->commitMasterChanges();
				}
			}
		} else {
			throw new \Exception( 'No route' );
		}
	}

	public function setRoute( base\Route $route ) {
		$this->route = $route;
	}

	public function getRoute() {
		return $this->route;
	}

	public function setReader( base\DataReader $reader ) {
		$this->reader = $reader;
	}

	public function getReader() {
		return $this->reader;
	}

	public function setWriter( base\DataWriter $writer ) {
		$this->writer = $writer;
	}

	public function getWriter() {
		return $this->writer;
	}
}