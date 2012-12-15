<?php
namespace REST\HTTP;

class DataReader extends \REST\base\DataReader {
	function __construct( $action = null ) {
		$qsData = $_GET;
		$data = array();

		//TODO: parsing the path should be moved
		//to a reusable component

		//PHP puts anything after filename.php/ (e.g. rest.php/aaa)
		//into a "title" key in the querystring array
		$tokens = explode( '/', $qsData['title'] );
		unset( $qsData['title'] );

		//support scope parameters
		if ( count( $tokens > 2 ) ) {
			$tokens = array_slice( $tokens, 2 );

			foreach( $tokens as &$t ) {
				//this parameters are in the URL
				//but not in the querystring so they need
				//to be manualle decoded
				$t = urldecode( $t );

				//support list of values for a parameter
				//e.g lat,lon
				if ( strpos( $t, ',' ) !== false ) {
					$t = explode( ',', $t );
				}
			}

			$data = $tokens;
		}

		switch ( $action ) {
			case 'update':
			case 'delete':
				// basically, we read a string from PHP's special input location,
				// and then parse it out into an array via parse_str... per the PHP docs:
				// Parses str  as if it were the query string passed via a URL and sets
				// variables in the current scope.
				$data += $this->parseRawRequest( file_get_contents( 'php://input' ) );
				break;
			case 'create':
				$data += $_POST;
				break;
			default:
			case 'read':
				$data += $qsData;
				break;
		}

		$this->values = $data;
	}

	/**
	 * Parse raw HTTP request data
	 *
	 * Pass in $a_data as an array. This is done by reference to avoid copying
	 * the data around too much.
	 *
	 * Any files found in the request will be added by their field name to the
	 * $data['files'] array.
	 *
	 * @param   array  Empty array to fill with data
	 * @return  array  Associative array of request data
	 */
	private function parseRawRequest( $requestData ) {
		$data = array();

		//grab multipart boundary from content type header
		preg_match( '/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

		//content type is probably regular form-encoded
		if ( !count( $matches ) ) {
			//we expect regular puts to containt a query string containing data
			parse_str( urldecode( $requestData ), $data );
			return $data;
		}

		$boundary = $matches[1];

		//split content by boundary and get rid of last -- element
		$blocks = preg_split( "/-+$boundary/", $requestData );
		array_pop( $blocks );

		//loop data blocks
		foreach ( $blocks as $id => $block ) {
			if ( empty( $block ) ) {
				continue;
			}

			$count = preg_match(
				'/Content-Disposition: form-data; name=\"([^\"]*)\"(; filename=\"([^\"]*)\")?[\n|\r]*([^\n\r].*[^\n\r])?[\r|\n]?$/ms',
				$block,
				$matches
			);

			if ( $count > 0 ) {
				$item = null;

				if ( empty( $matches[1] ) ) {
					continue;
				}

				$name = $matches[1];

				if ( !empty( $matches[3] ) ) {
					$item = array(
						'filename' => $matches[3],
						'content' => $matches[4]
					);
				} else {
					$item = $matches[4];
				}

				$data[$name] = $item;
			}
		}

		return $data;
	}
}
