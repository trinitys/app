<?php
namespace REST\HTTP;

class DataReader extends \REST\base\DataReader {
	function __construct() {
		$this->values = $_GET + $_POST;
	}
}