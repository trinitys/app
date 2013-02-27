<?php

require_once( 'WikiaSearchBaseTest.php' );

class WikiaSearchResultSetTest extends WikiaSearchBaseTest
{
	protected $config;
	protected $resultSet;
	protected $searchResult;

	/**
	 * Convenience method to easily handle the necessary dependencies & method mocking for recurrent mocks
	 * @param array $resultSetMethods
	 * @param array $configMethods
	 * @param array $resultMethods
	 */
	protected function prepareMocks( $resultSetMethods = array(), $configMethods = array(), $resultMethods = array() ) { 
	
		$this->searchResult		=	$this->getMockBuilder( 'Solarium_Result_Select' )
									->disableOriginalConstructor()
									->setMethods( $resultMethods )
									->getMock();
		
		$this->config		=	$this->getMockBuilder( 'WikiaSearchConfig' )
									->disableOriginalConstructor()
									->setMethods( $configMethods )
									->getMock();
		
		$this->resultSet	=	$this->getMockBuilder( 'WikiaSearchResultSet' )
									->disableOriginalConstructor()
									->setMethods( $resultSetMethods )
									->getMock();
		
		$reflResult = new ReflectionProperty( 'WikiaSearchResultSet', 'searchResultObject' );
		$reflResult->setAccessible( true );
		$reflResult->setValue( $this->resultSet, $this->searchResult );
		
		$reflConfig = new ReflectionProperty( 'WikiaSearchResultSet', 'searchConfig' );
		$reflConfig->setAccessible( true );
		$reflConfig->setValue( $this->resultSet, $this->config );
	}
	
	/**
	 * @covers WikiaSearchResultSet::__construct
	 */
	public function testConstructor() {
		$this->prepareMocks();
		
		$metapos = 2;
		
		$set = $this->getMockBuilder( 'WikiaSearchResultSet' )
					->setMethods( array( 'configure' ) )
					->setConstructorArgs( array( $this->searchResult, $this->config, $this->resultSet, $metapos ) )
					->getMock();
		
		$expectedMemberVars = array(
				'searchResultObject'	=>	$this->searchResult,
				'searchConfig'			=>	$this->config,
				'parent'				=>	$this->resultSet,
				'metaposition'			=>	$metapos
				);
		
		foreach ( $expectedMemberVars as $name => $val ) {
			$refl = new ReflectionProperty( 'WikiaSearchResultSet', $name );
			$refl->setAccessible( true );
			$this->assertEquals(
					$val,
					$refl->getValue( $set ),
					"WikiaSearchResultSet->{$name} should be set during __construct"
			);
		}
	
	}
	
	/**
	 * @covers WikiaSearchResultSet::configure
	 */
	public function testResultSetWithEmpty() {
		$this->prepareMocks();
		
		$this->searchResult = $this->getMock( 'Solarium_Result_Select_Empty' );
		
		$reflResult = new ReflectionProperty( 'WikiaSearchResultSet', 'searchResultObject' );
		$reflResult->setAccessible( true );
		$reflResult->setValue( $this->resultSet, $this->searchResult );
		
		$this->resultSet
			->expects	( $this->never() )
			->method	( 'configureGroupedSetAsRootNode' )
		;
		$this->resultSet
			->expects	( $this->never() )
			->method	( 'configureGroupedSetAsLeafNode' )
		;
		$this->resultSet
			->expects	( $this->never() )
			->method	( 'configureUngroupedSet' )
		;
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configure' );
		$configureRefl->setAccessible( true );
		
		$this->assertTrue(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configure should always return true' 
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::configure
	 */
	public function testResultSetUngrouped() {
		$resultSetMethods = array(
				'configureGroupedSetAsRootNode',
				'configureGroupedSetAsLeafNode',
				'configureUngroupedSet'
				);
		$configMethods = array(
				);
		$resultMethods = array(
				);
		
		$this->prepareMocks( $resultSetMethods, $configMethods, $resultMethods );
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'configureGroupedSetAsRootNode' )
			->will		( $this->returnValue( false ) )
		;
		$this->resultSet
			->expects	( $this->at( 1 ) )
			->method	( 'configureGroupedSetAsLeafNode' )
			->will		( $this->returnValue( false ) )
		;
		$this->resultSet
			->expects	( $this->at( 2 ) )
			->method	( 'configureUngroupedSet' )
			->will		( $this->returnValue( true ) );
		;
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configure' );
		$configureRefl->setAccessible( true );
		
		$this->assertTrue(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configure should always return true' 
		);
		
	}
	

	/**
	 * @covers WikiaSearchResultSet::configure
	 */
	public function testResultSetGroupedLeaf() {
		$resultSetMethods = array(
				'configureGroupedSetAsRootNode',
				'configureGroupedSetAsLeafNode',
				'configureUngroupedSet'
				);
		$configMethods = array(
				);
		$resultMethods = array(
				);
		
		$this->prepareMocks( $resultSetMethods, $configMethods, $resultMethods );
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'configureGroupedSetAsRootNode' )
			->will		( $this->returnValue( false ) )
		;
		$this->resultSet
			->expects	( $this->at( 1 ) )
			->method	( 'configureGroupedSetAsLeafNode' )
			->will		( $this->returnValue( true ) )
		;
		$this->resultSet
			->expects	( $this->never() )
			->method	( 'configureUngroupedSet' )
		;
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configure' );
		$configureRefl->setAccessible( true );
		
		$this->assertTrue(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configure should always return true' 
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::configure
	 */
	public function testResultSetGroupedRoot() {
		$resultSetMethods = array(
				'configureGroupedSetAsRootNode',
				'configureGroupedSetAsLeafNode',
				'configureUngroupedSet'
				);
		$configMethods = array(
				);
		$resultMethods = array(
				);
		
		$this->prepareMocks( $resultSetMethods, $configMethods, $resultMethods );
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'configureGroupedSetAsRootNode' )
			->will		( $this->returnValue( true ) )
		;
		$this->resultSet
			->expects	( $this->never() )
			->method	( 'configureGroupedSetAsLeafNode' )
		;
		$this->resultSet
			->expects	( $this->never() )
			->method	( 'configureUngroupedSet' )
		;
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configure' );
		$configureRefl->setAccessible( true );
		
		$this->assertTrue(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configure should always return true' 
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::configureGroupedSetAsRootNode
	 */
	public function testConfigureGroupedSetAsRootNodeSatisfied() {
		$resultSetMethods = array(
				'setResultGroupings',
				'setResultsFound',
				'getHostGrouping'
				);
		$configMethods = array( 'getGroupResults' );
		$this->prepareMocks( $resultSetMethods, $configMethods );
		
		$mockGrouping = $this->getMockBuilder( 'Solarium_Result_Select_Grouping' )
							->disableOriginalConstructor()
							->getMock();
		
		$mockFieldGroup = $this->getMockBuilder( 'Solarium_Result_Select_Grouping_FieldGroup' )
							->disableOriginalConstructor()
							->setMethods( array( 'getMatches' ) )
							->getMock();
		
		$matchesCount = 2000;
		
		$this->config
			->expects	( $this->at( 0 ) )
			->method	( 'getGroupResults' )
			->will		( $this->returnValue ( $mockGrouping ) )
		;
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'setResultGroupings' )
		;
		$this->resultSet
			->expects	( $this->at( 1 ) )
			->method	( 'getHostGrouping' )
			->will		( $this->returnValue( $mockFieldGroup ) )
		;
		$mockFieldGroup
			->expects	( $this->at( 0 ) )
			->method	( 'getMatches' )
			->will		( $this->returnValue( $matchesCount ) )
		;
		$this->resultSet
			->expects	( $this->at( 2 ) )
			->method	( 'setResultsFound' )
			->with		( $matchesCount )
		;
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configureGroupedSetAsRootNode' );
		$configureRefl->setAccessible( true );
		
		$this->assertTrue(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configureGroupedSetAsRootNode should return true if the constructed result set satisfies its necessary criteria' 
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::configureGroupedSetAsRootNode
	 */
	public function testConfigureGroupedSetAsRootNodeUnsatisfied() {
		$resultSetMethods = array(
				);
		$configMethods = array( 'getGroupResults' );
		$this->prepareMocks( $resultSetMethods );
		
		$this->config
			->expects	( $this->at( 0 ) )
			->method	( 'getGroupResults' )
			->will		( $this->returnValue ( false ) )
		;
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configureGroupedSetAsRootNode' );
		$configureRefl->setAccessible( true );
		
		$this->assertFalse(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configureGroupedSetAsRootNode should return true if the constructed result set satisfies its necessary criteria' 
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::configureGroupedSetAsLeafNode
	 */
	public function testConfigureGroupedSetAsLeafNodeUnsatisfied() {
		$this->prepareMocks();
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configureGroupedSetAsLeafNode' );
		$configureRefl->setAccessible( true );
		
		// this works because the parent and metaposition values need to be set to satisfy
		
		$this->assertFalse(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configureGroupedSetAsLeafNode should return false if the constructed result set doesn\'t satisfy its necessary criteria' 
		);
	}

	/**
	 * @covers WikiaSearchResultSet::configureUngroupedSet
	 */
	public function testConfigureUngroupedSet() {
		$this->prepareMocks( array( 'prependArticleMatchIfExists', 'setResults', 'setResultsFound' ), array(), array( 'getDocuments', 'getNumFound' ) );
		
		$documents = array( 'foo' ); // doesn't really matter
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'prependArticleMatchIfExists' )
			->will		( $this->returnValue( $this->resultSet ) )
		;
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getDocuments' )
			->will		( $this->returnValue( $documents ) )
		;
		$this->resultSet
			->expects	( $this->at( 1 ) )
			->method	( 'setResults' )
			->with		( $documents )
			->will		( $this->returnValue( $this->resultSet ) )
		;
		$this->searchResult
			->expects	( $this->at( 1 ) )
			->method	( 'getNumFound' )
			->will		( $this->returnValue( 1 ) )
		;
		$this->resultSet
			->expects	( $this->at( 2 ) )
			->method	( 'setResultsFound' )
			->with		( 2 )
		;
		
		// this mocks an article match, which will increment this property
		$resRefl = new ReflectionProperty( 'WikiaSearchResultSet', 'resultsFound' );
		$resRefl->setAccessible( true );
		$resRefl->setValue( $this->resultSet, 1 );
		
		$configureRefl = new ReflectionMethod( 'WikiaSearchResultSet', 'configureUngroupedSet' );
		$configureRefl->setAccessible( true );
		
		$this->assertTrue(
				$configureRefl->invoke( $this->resultSet ),
				'WikiaSearchResultSet::configureUngroupedSet should always return true' 
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getHostGrouping
	 */
	public function testGetHostGroupingWithoutGrouping() {
		$this->prepareMocks( array(), array(), array( 'getGrouping' ) );
		
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getGrouping' )
			->will		( $this->returnValue( null ) )
		;
		
		$method = new ReflectionMethod( 'WikiaSearchResultSet', 'getHostGrouping' );
		$method->setAccessible( true );
		
		try {
			$method->invoke( $this->resultSet );
		} catch ( Exception $e ) { }
		
		$this->assertInstanceOf( 
				'Exception', 
				$e,
				'WikiaSearchResultSet::getHostGrouping should throw an exception if called in a situation where we are not grouping results'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getHostGrouping
	 */
	public function testGetHostGroupingWithoutHostGrouping() {
		$this->prepareMocks( array(), array(), array( 'getGrouping' ) );
		
		$mockGrouping = $this->getMockBuilder( 'Solarium_Result_Select_Grouping' )
							->disableOriginalConstructor()
							->setMethods( array( 'getGroup' ) )
							->getMock();
		
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getGrouping' )
			->will		( $this->returnValue( $mockGrouping ) )
		;
		$mockGrouping
			->expects	( $this->at( 0 ) )
			->method	( 'getGroup' )
			->with		( 'host' )
			->will		( $this->returnValue( null ) )
		;
		
		$method = new ReflectionMethod( 'WikiaSearchResultSet', 'getHostGrouping' );
		$method->setAccessible( true );
		
		try {
			$method->invoke( $this->resultSet );
		} catch ( Exception $e ) { }
		
		$this->assertInstanceOf( 
				'Exception', 
				$e,
				'WikiaSearchResultSet::getHostGrouping should throw an exception if called in a situation where we are not grouping results by host'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getHostGrouping
	 */
	public function testGetHostGroupingWorks() {
		
		$this->prepareMocks( array(), array(), array( 'getGrouping' ) );
		
		$mockGrouping = $this->getMockBuilder( 'Solarium_Result_Select_Grouping' )
							->disableOriginalConstructor()
							->setMethods( array( 'getGroup' ) )
							->getMock();
		
		$mockFieldGroup = $this->getMockBuilder( 'Solarium_Result_Select_Grouping_FieldGroup' )
							->disableOriginalConstructor()
							->setMethods( array( 'getValueGroups' ) )
							->getMock();
		
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getGrouping' )
			->will		( $this->returnValue( $mockGrouping ) )
		;
		$mockGrouping
			->expects	( $this->at( 0 ) )
			->method	( 'getGroup' )
			->with		( 'host' )
			->will		( $this->returnValue( $mockFieldGroup ) )
		;
		
		$method = new ReflectionMethod( 'WikiaSearchResultSet', 'getHostGrouping' );
		$method->setAccessible( true );
		
		$this->assertEquals(
				$mockFieldGroup,
				$method->invoke( $this->resultSet ),
				'WikiaSearchResultSet::getHostGrouping should return an instance of Solarium_Result_Select_Grouping_FieldGroup'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::setResultGroupings
	 */
	public function testSetResultGroupings() {
		$this->prepareMocks( array( 'getHostGrouping', 'getHeader' ) );
		
		$mockFieldGroup = $this->getMockBuilder( 'Solarium_Result_Select_Grouping_FieldGroup' )
							->disableOriginalConstructor()
							->setMethods( array( 'getValueGroups' ) )
							->getMock();
		
		$mockValueGroup = $this->getMockBuilder( 'Solarium_Result_Select_Grouping_ValueGroup' )
							->disableOriginalConstructor()
							->setMethods( array( 'getNumFound', 'getValue', 'getDocuments' ) )
							->getMock();
		
		$resultSet2 = clone $this->resultSet;
		
		$url = 'http://foo.wikia.com';
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'getHostGrouping' )
			->will		( $this->returnValue( $mockFieldGroup ) )
		;
		$mockFieldGroup
			->expects	( $this->at( 0 ) )
			->method	( 'getValueGroups' )
			->will		( $this->returnValue( array( 'foo' ) ) ) // value doesn't matter
		;
		$resultSet2
			->expects	( $this->at( 0 ) )
			->method	( 'getHeader' )
			->will		( $this->returnValue( $url ) )
		;
		
		$this->mockClass( 'WikiaSearchResultSet', $resultSet2 );
		$this->mockApp();
		
		$method = new ReflectionMethod( 'WikiaSearchResultSet', 'setResultGroupings' );
		$method->setAccessible( true );
		
		$this->assertEquals(
				$this->resultSet,
				$method->invoke( $this->resultSet ),
				'WikiaSearchResultSet::setResultGrouping should provide a fluent interface'
		);
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		
		$this->assertArrayHasKey(
				$url,
				$results->getValue( $this->resultSet ),
				'WikiaSearchResultSet::setResultGroupings should set instances of WikiaSearchResultSet as values keyed by their URL in the parent\'s $result attribute'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::setResults
	 */
	public function testSetResults() {
		$this->prepareMocks( array( 'addResult' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->getMock(); 
		
		$this->resultSet
			->expects	( $this->once() )
			->method	( 'addResult' )
			->with		( $mockResult )
		;
		
		$this->assertEquals(
				$this->resultSet,
				$this->resultSet->setResults( array( $mockResult ) ),
				'WikiaSearchResultSet::setResults should provide a fluent interface'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::setResultsFound
	 */
	public function testSetResultsFound() {
		$this->prepareMocks( array( 'prependArticleMatchIfExists' ) ); // not providing a method to mock makes our mock result set cranky
		
		$this->assertEquals(
				$this->resultSet,
				$this->resultSet->setResultsFound( 10 ),
				'WikiaSearchResultSet::setResultsFound should provide a fluent interface'
		);
		
		$property = new ReflectionProperty( 'WikiaSearchResultSet', 'resultsFound' );
		$property->setAccessible( true );
		
		$this->assertEquals(
				10,
				$property->getValue( $this->resultSet ),
				'WikiaSearchResultSet::setResultsFound should set the resultsFound variable'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::prependArticleMatchIfExists
	 */
	public function testPrependArticleMatchIfExistsNoMatch() {
		$this->prepareMocks( array( 'getResultsStart', 'addResult' ), array( 'hasArticleMatch', 'getArticleMatch' ) );
		
		$this->config
			->expects	( $this->at( 0 ) )
			->method	( 'hasArticleMatch' )
			->will		( $this->returnValue( false ) )
		;
		$this->resultSet
			->expects	( $this->never() )		//should be short-circuited
			->method	( 'getResultsStart' )
		;
		
		$prepend = new ReflectionMethod( 'WikiaSearchResultSet', 'prependArticleMatchIfExists' );
		$prepend->setAccessible( true );
		
		$this->assertEquals(
				$this->resultSet,
				$prepend->invoke( $this->resultSet ),
				'WikiaSearchResultSet::prependArticleMatchIfExists should provide a fluent interface'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::prependArticleMatchIfExists
	 */
	public function testPrependArticleMatchIfExistsMatchWithPagination() {
		$this->prepareMocks( array( 'getResultsStart', 'addResult' ), array( 'hasArticleMatch', 'getArticleMatch' ) );
		
		$this->config
			->expects	( $this->at( 0 ) )
			->method	( 'hasArticleMatch' )
			->will		( $this->returnValue( true ) )
		;
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'getResultsStart' )
			->will		( $this->returnValue( 20 ) )
		;
		$this->config
			->expects	( $this->never() )
			->method	( 'getArticleMatch' )
		;
		
		$prepend = new ReflectionMethod( 'WikiaSearchResultSet', 'prependArticleMatchIfExists' );
		$prepend->setAccessible( true );
		
		$this->assertEquals(
				$this->resultSet,
				$prepend->invoke( $this->resultSet ),
				'WikiaSearchResultSet::prependArticleMatchIfExists should provide a fluent interface'
		);
	}
	

	/**
	 * @covers WikiaSearchResultSet::addResult
	 */
	public function testAddResultInvalid() {
		$this->prepareMocks( array( 'isValidResult' ) );
		
		$mockSearchResult		= $this->getMockBuilder( 'WikiaSearchResult' )
									->disableOriginalConstructor()
									->getMock();
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'isValidResult' )
			->with		( $mockSearchResult )
			->will		( $this->returnValue( false ) )
		;
		
		$addResult = new ReflectionMethod( 'WikiaSearchResultSet', 'addResult' );
		$addResult->setAccessible( true );
		
		try {
			$addResult->invoke( $this->resultSet, $mockSearchResult );
		} catch ( Exception $e ) { }
		
		$this->assertInstanceOf(
				'WikiaException',
				$e,
				'WikiaSearchResultSet::addResult should throw an exception if attempting to add an invalid result'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::addResult
	 */
	public function testAddResultValid() {
		$this->prepareMocks( array( 'isValidResult' ), array(), array( 'getHighlighting' ) );
		
		$mockSearchResult		= $this->getMockBuilder( 'WikiaSearchResult' )
									->disableOriginalConstructor()
									->setMethods( array( 'offsetGet', 'setText', 'setVar', 'getVar' ) )
									->getMock();
		
		$mockHighlighting		= $this->getMockBuilder( 'Solarium_Result_Select_Highlighting' )
									->disableOriginalConstructor()
									->setMethods( array( 'getResult' ) )
									->getMock();
		
		$mockHighlightingResult	= $this->getMockBuilder( 'Solarium_Result_Select_Highlighting_Result' )
									->disableOriginalConstructor()
									->setMethods( array( 'getField' ) )
									->getMock();
		
		$mockLang				= $this->getMockBuilder( 'Language' )
									->disableOriginalConstructor()
									->setMethods( array( 'date' ) )
									->getMock();
		
		$mockWf					= $this->getMockBuilder( 'WikiaFunctionWrapper' )
									->disableOriginalConstructor()
									->setMethods( array( 'Timestamp' ) )
									->getMock();
		
		$mockId = "123_456";
		$mockHlSnippet = "I can't believe it's not <em>butter</em>!";
		$mockTimestamp = date( 'Y-m-d' ).'T00:00:00Z'; // always today
		$wfts = wfTimestamp( TS_MW, $mockTimestamp );
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'isValidResult' )
			//->with		( $mockSearchResult )
			->will		( $this->returnValue( true ) )
		;
		$mockSearchResult
			->expects	( $this->at( 0 ) )
			->method	( 'offsetGet' )
			->with		( 'id' )
			->will		( $this->returnValue( $mockId ) )
		;
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getHighlighting' )
			->will		( $this->returnValue( $mockHighlighting ) )
		;
		$mockHighlighting
			->expects	( $this->at( 0 ) )
			->method	( 'getResult' )
			->with		( $mockId )
			->will		( $this->returnValue( $mockHighlightingResult ) )
		;
		$mockHighlightingResult
			->expects	( $this->at( 0 ) )
			->method	( 'getField' )
			->with		( WikiaSearch::field( 'html' ) )
			->will		( $this->returnValue( array( $mockHlSnippet ) ) )
		;
		$mockSearchResult
			->expects	( $this->at( 1 ) )
			->method	( 'setText' )
			->with		( $mockHlSnippet )
		;
		$mockSearchResult
			->expects	( $this->at( 2 ) )
			->method	( 'offsetGet' )
			->with		( 'created' )
			->will		( $this->returnValue( $mockTimestamp ) )
		;
		$mockSearchResult
			->expects	( $this->at( 3 ) )
			->method	( 'setVar' )
			->with		( 'created', $mockTimestamp )
			->will		( $this->returnValue( $mockSearchResult ) )
		;
		$mockWf
			->expects	( $this->at( 0 ) )
			->method	( 'Timestamp' )
			->with		( TS_MW, $mockTimestamp )
			->will		( $this->returnValue( $wfts ) )
		;
		$mockLang
			->expects	( $this->at( 0 ) )
			->method	( 'date' )
			->with		( $wfts )
			->will		( $this->returnValue( 'Today' ) )
		;
		$mockSearchResult
			->expects	( $this->at( 4 ) )
			->method	( 'setVar' )
			->with		( 'fmt_timestamp', 'Today' )
			->will		( $this->returnValue( $mockSearchResult ) )
		;
		$mockSearchResult
			->expects	( $this->at( 5 ) )
			->method	( 'getVar' )
			->with		( 'fmt_timestamp' )
			->will		( $this->returnValue( 'Today' ) )
		;
		$mockSearchResult
			->expects	( $this->at( 6 ) )
			->method	( 'setVar' )
			->with		( 'created_30daysago', false )
			->will		( $this->returnValue( $mockSearchResult ) )
		;
		$mockSearchResult
			->expects	( $this->at( 7 ) )
			->method	( 'offsetGet' )
			->with		( WikiaSearch::field( 'categories' ) )
			->will		( $this->returnValue( null ) )
		;
		$mockSearchResult
			->expects	( $this->at( 8 ) )
			->method	( 'setVar' )
			->with		( 'categories', 'NONE' )
			->will		( $this->returnValue( $mockSearchResult ) )
		;
		$mockSearchResult
			->expects	( $this->at( 9 ) )
			->method	( 'offsetGet' )
			->with		( 'wikiarticles' )
			->will		( $this->returnValue( 12345 ) )
		;
		$mockSearchResult
			->expects	( $this->at( 10 ) )
			->method	( 'setVar' )
			->with		( 'cityArticlesNum', 12345 )
			->will		( $this->returnValue( $mockSearchResult ) )
		;
		$mockSearchResult
			->expects	( $this->at( 11 ) )
			->method	( 'offsetGet' )
			->with		( WikiaSearch::field( 'wikititle' ) )
			->will		( $this->returnValue( "My Wiki" ) )
		;
		$mockSearchResult
			->expects	( $this->at( 12 ) )
			->method	( 'setVar' )
			->with		( 'wikititle', "My Wiki" )
			->will		( $this->returnValue( $mockSearchResult ) )
		;
		
		
		$mockWg = (object) array(
				'Lang'	=>	$mockLang,
				);
		
		$wrapper = new ReflectionProperty( 'WikiaSearchResultSet', 'wf' );
		$wrapper->setAccessible( true );
		$wrapper->setValue( $this->resultSet, $mockWf );
		
		$global = new ReflectionProperty( 'WikiaSearchResultSet', 'wg' );
		$global->setAccessible( true );
		$global->setValue( $this->resultSet, $mockWg );
		
		$addResult = new ReflectionMethod( 'WikiaSearchResultSet', 'addResult' );
		$addResult->setAccessible( true );

		$this->assertEquals(
				$this->resultSet,
				$addResult->invoke( $this->resultSet, $mockSearchResult ),
				'WikiaSearchResultSet::addResult should provide a fluent interface'
		);
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		
		$this->assertArrayHasKey(
				$mockId,
				$results->getValue( $this->resultSet ),
				'WikiaSearchResultSet::addResult should add a result to the results array keyed by the ID of the document'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getResultsFound
	 */
	public function testGetResultsFound() {
		$this->prepareMocks( array( 'getResultsStart' ) );
		
		$rf = new ReflectionProperty( 'WikiaSearchResultSet', 'resultsFound' );
		$rf->setAccessible( true );
		$rf->setValue( $this->resultSet, 20 );
		
		$this->assertEquals(
				20,
				$this->resultSet->getResultsFound(),
				'WikiaSearchResultSet::getResultsFound should return the value set in the "resultFound" property'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getResultsStart
	 */
	public function testGetResultsStart() {
		$this->prepareMocks( array( 'getResultsFound' ), array(), array( 'getStart' ) );
		
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getStart' )
			->will		( $this->returnValue( 0 ) )
		;
		$this->assertEquals(
				0,
				$this->resultSet->getResultsStart(),
				'WikiaSearchResultSet::getResultsStart should pass the return value of the search result object\'s getStart() method'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getResultsNum
	 * @covers WikiaSearchResultSet::hasResults
	 */
	public function testResultCountMethods() {
		$this->prepareMocks( array( 'getResultsFound' ) );
		
		$this->assertEquals(
				0,
				$this->resultSet->getResultsNum(),
				'WikiaSearchResultSet::getResultsNum should return 0 if there are no results'
		);
		$this->assertFalse(
				$this->resultSet->hasResults(),
				'WikiaSearchResultSet::hasResults should return false if there are no results'
		);
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->getMock();
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, array( '123_234' => $mockResult ) );
		
		$this->assertEquals(
				1,
				$this->resultSet->getResultsNum(),
				'WikiaSearchResultSet::getResultsNum should return the count of the result array'
		);
		$this->assertTrue(
				$this->resultSet->hasResults(),
				'WikiaSearchResultSet::hasResults should return false if there are any results'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::next
	 * @covers WikiaSearchResultSet::rewind
	 * @covers WikiaSearchResultSet::current
	 * @covers WikiaSearchResultSet::key
	 * @covers WikiaSearchResultSet::valid
	 */
	public function testIteratorMethods() {
		$this->prepareMocks( array( 'hasArticleMatch' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->setMethods( array( 'getID' ) )
							->getMock();
		
		$mockResult2 = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->setMethods( array( 'getTitle' ) )
							->getMock();
		
		$resultArray = array( '123_234' => $mockResult, '345_456' => $mockResult2 );
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, $resultArray );
		
		$this->resultSet->rewind();
		$this->assertEquals(
				$mockResult,
				$this->resultSet->next()
		);
		$this->assertEquals(
				$mockResult2,
				$this->resultSet->current()
		);
		$this->assertEquals(
				1,
				$this->resultSet->key()
		);
		$this->assertTrue(
				$this->resultSet->valid()
		);
		$this->resultSet->rewind();
		$this->assertEquals(
				$mockResult,
				$this->resultSet->current()
		);
		
		$this->prepareMocks( array( 'valid' ) );
		
		$this->resultSet
			->expects	( $this->once() )
			->method	( 'valid' )
			->will		( $this->returnValue( false ) )
		;
		$this->assertFalse(
				$this->resultSet->current()
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::setHeader
	 * @covers WikiaSearchResultSet::getHeader
	 */
	public function testSetHeader() {
		$this->prepareMocks( array( 'hasArticleMatch' ) );
		
		$this->assertEquals(
				'foo',
				$this->resultSet->setHeader( 'bar', 'foo' )->getHeader( 'bar' ),
				'WikiaSearchResultSet::setHeader should provide a fluent interface and set the value for the key in the headers array. ' .
				'Calling getHeader() should return the value for the provided key.'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::isValidResult
	 */
	public function testIsValidResult() {
		$this->prepareMocks( array( 'hasArticleMatch' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->getMock();
		
		$isValid = new ReflectionMethod( 'WikiaSearchResultSet', 'isValidResult' );
		$isValid->setAccessible( true );
		
		$this->assertTrue(
				$isValid->invoke( $this->resultSet, $mockResult ),
				'WikiaSearchResult::isValidResult should return true if the provided argument is an instance of WikiaSearchResult'
		);
		$this->assertTrue(
				$isValid->invoke( $this->resultSet, $this->resultSet ),
				'WikiaSearchResult::isValidResult should return true if the provided argument is an instance of WikiaSearchResultSet'
		);
		$this->assertFalse(
				$isValid->invoke( $this->resultSet, $this->searchResult ),
				'WikiaSearchResult::isValidResult should return false for items that are not WikiaSearchResult or WikiaSearchResultSet'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getQuery
	 */
	public function testGetQuery() {
		$this->prepareMocks( array( 'hasArticleMatch' ), array( 'getQuery' ) );
		
		$this->config
			->expects	( $this->at( 0 ) )
			->method	( 'getQuery' )
			->with		( WikiaSearchConfig::QUERY_ENCODED )
			->will		( $this->returnValue( 'query' ) )
		;
		$this->assertEquals(
				'query',
				$this->resultSet->getQuery( WikiaSearchConfig::QUERY_ENCODED ),
				'WikiaSearchResultSet::getQuery should return the value of WikiaSearchConfig::getQuery, encoded'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getQueryTime
	 */
	public function testGetQueryTime() {
		$this->prepareMocks( array( 'hasArticleMatch' ), array(), array( 'getQueryTime' ) );
		
		$this->searchResult
			->expects	( $this->at( 0 ) )
			->method	( 'getQueryTime' )
			->will		( $this->returnValue( 750 ) )
		;
		$this->assertEquals(
				750,
				$this->resultSet->getQueryTime(),
				'WikiaSearchResultSet::getQueryTime should return the value of WikiaSearchConfig::getQueryTime'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::isOnlyArticleMatchFound
	 */
	public function testIsOnlyArticleMatchFoundWrongResultNum() {
		$this->prepareMocks( array( 'getResultsNum' ) );
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'getResultsNum' )
			->will		( $this->returnValue( 0 ) )
		;
		
		$this->assertFalse(
				$this->resultSet->isOnlyArticleMatchFound(),
				'WikiaSearchResultSet::isOnlyArticleMatchFound should return false unless there is only one result, and it is marked as an article match'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::isOnlyArticleMatchFound
	 */
	public function testIsOnlyArticleMatchFoundNotArticleMatch() {
		$this->prepareMocks( array( 'getResultsNum' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->setMethods( array( 'getVar' ) )
							->getMock();
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'getResultsNum' )
			->will		( $this->returnValue( 1 ) )
		;
		
		$mockResult
			->expects	( $this->at( 0 ) )
			->method	( 'getVar' )
			->with		( 'isArticleMatch' )
			->will		( $this->returnValue( false ) )
		;

		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, array( $mockResult ) );
		
		$this->assertFalse(
				$this->resultSet->isOnlyArticleMatchFound(),
				'WikiaSearchResultSet::isOnlyArticleMatchFound should return false unless there is only one result, and it is marked as an article match'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::isOnlyArticleMatchFound
	 */
	public function testIsOnlyArticleMatchFoundCorrect() {
		$this->prepareMocks( array( 'getResultsNum' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->setMethods( array( 'getVar' ) )
							->getMock();
		
		$this->resultSet
			->expects	( $this->at( 0 ) )
			->method	( 'getResultsNum' )
			->will		( $this->returnValue( 1 ) )
		;
		
		$mockResult
			->expects	( $this->at( 0 ) )
			->method	( 'getVar' )
			->with		( 'isArticleMatch' )
			->will		( $this->returnValue( true ) )
		;

		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, array( $mockResult ) );
		
		$this->assertTrue(
				$this->resultSet->isOnlyArticleMatchFound(),
				'WikiaSearchResultSet::isOnlyArticleMatchFound should return true when there is only one result, and it is marked as an article match'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::offsetExists
	 * @covers WikiaSearchResultSet::offsetGet
	 * @covers WikiaSearchResultSet::offsetSet
	 * @covers WikiaSearchResultSet::offsetUnset
	 */
	public function testArrayAccess() {
		$this->prepareMocks( array( 'getResultsNum' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->setMethods( array( 'getVar' ) )
							->getMock();
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, array( $mockResult ) );
		
		$this->assertTrue(
				$this->resultSet->offsetExists( 0 )
		);
		$this->assertFalse(
				$this->resultSet->offsetExists( 1 )
		);
		$this->assertEquals(
				$mockResult,
				$this->resultSet[0]
		);
		$this->assertFalse(
				$this->resultSet[1]
		);
		$this->resultSet[1] = clone $mockResult;
		$this->assertNotEmpty(
				$this->resultSet[1]
		);
		unset( $this->resultSet[1] );
		$this->assertFalse(
				$this->resultSet[1]
		); 
	}
	
	/**
	 * @covers WikiaSearchResultSet::getParent
	 */
	public function testGetParent() {
		$this->prepareMocks( array( 'getId' ) );
		
		$resultSet2 = clone $this->resultSet;
		
		$parent = new ReflectionProperty( 'WikiaSearchResultSet', 'parent' );
		$parent->setAccessible( true );
		$parent->setValue( $this->resultSet, $resultSet2 );
		
		$this->assertEquals(
				$resultSet2,
				$this->resultSet->getParent(),
				'WikiaSearchResultSet::getParent() should return the value set in the parent attribute'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getId
	 */
	public function testGetId() {
		$this->prepareMocks( array( 'getResultsNum' ) );
		
		$host = new ReflectionProperty( 'WikiaSearchResultSet', 'host' );
		$host->setAccessible( true );
		$host->setValue( $this->resultSet, 'foo.wikia.com' );
		
		$this->assertEquals(
				'foo.wikia.com',
				$this->resultSet->getId(),
				'WikiaSearchResultSet::getId() should return the value of the host property if it is set'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::getResults
	 */
	public function testGetResults() {
		$this->prepareMocks( array( 'prependArticleMatchIfExists' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->getMock();
		
		$mockResults = array( $mockResult );
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, $mockResults );
		
		$this->assertEquals(
				$mockResults,
				$this->resultSet->getResults(),
				'WikiaSearchResultSet::getResults should return the value of the results property'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::toNestedArray
	 */
	public function testToNestedArray() {
		$this->prepareMocks( array( 'getResults' ) );
		
		$mockResult = $this->getMockBuilder( 'WikiaSearchResult' )
							->disableOriginalConstructor()
							->setMethods( array( 'toArray' ) )
							->getMock();
		
		$resultArray = array( 'title' => "mytitle", 'url' => 'myurl' );
		
		$mockResult
			->expects	( $this->at( 0 ) )
			->method	( 'toArray' )
			->with		( array( 'title', 'url' ) )
			->will		( $this->returnValue( $resultArray ) )
		;
		
		$mockResults = array( $mockResult );
		
		$results = new ReflectionProperty( 'WikiaSearchResultSet', 'results' );
		$results->setAccessible( true );
		$results->setValue( $this->resultSet, $mockResults );
		
		$this->assertEquals(
				array( $resultArray ),
				$this->resultSet->toNestedArray(),
				'WikiaSearchResultSet should return an array of results that have been transformed to array'
		);
	}
	
	/**
	 * @covers WikiaSearchResultSet::toArray
	 */
	public function testToArray() {
		
		$mockSet = $this->getMockBuilder( 'WikiaSearchResultSet' )
		                ->disableOriginalConstructor()
		                ->setMethods( array( 'getHeader' ) )
		                ->getMock();
		
		$title = "Kendrick Lamar Wiki";
		$url = "http://goodkidmaadwikicities.wikia.com/";
		
		$mockSet
		    ->expects( $this->at( 0 ) )
		    ->method ( 'getHeader' )
		    ->with   ( 'cityTitle' )
		    ->will   ( $this->returnValue( $title ) )
		;
		$mockSet
		    ->expects( $this->at( 1 ) )
		    ->method ( 'getHeader' )
		    ->with   ( 'cityUrl' )
		    ->will   ( $this->returnValue( $url ) )
		;
		$this->assertEquals(
				array( 'title' => $title, 'url' => $url ),
				$mockSet->toArray()
		);
		
	}
}