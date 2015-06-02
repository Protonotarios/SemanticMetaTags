<?php

namespace SMT\Tests;

use SMT\HookRegistry;
use Title;

/**
 * @covers \SMT\HookRegistry
 *
 * @group semantic-meta-tags
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$configuration =  array();

		$this->assertInstanceOf(
			'\SMT\HookRegistry',
			new HookRegistry( $store, $configuration )
		);
	}

	public function testRegister() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$configuration = array(
			'metaTagsContentPropertySelector' => array(),
			'metaTagsStaticContentDescriptor' => array(),
			'metaTagsBlacklist' => array(),
			'metaTagsFallbackUseForMultipleProperties' => false
		);

		$instance = new HookRegistry( $store, $configuration );
		$instance->register();

		$this->doTestRegisteredOutputPageParserOutputHandler( $instance );
	}

	public function doTestRegisteredOutputPageParserOutputHandler( $instance ) {

		$this->assertTrue(
			$instance->isRegistered( 'OutputPageParserOutput' )
		);

		$title = Title::newFromText( __METHOD__ );

		$webRequest = $this->getMockBuilder( '\WebRequest' )
			->disableOriginalConstructor()
			->getMock();

		$context = $this->getMockBuilder( '\IContextSource' )
			->disableOriginalConstructor()
			->getMock();

		$context->expects( $this->atLeastOnce() )
			->method( 'getRequest' )
			->will( $this->returnValue( $webRequest ) );

		$outputPage = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$outputPage->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$outputPage->expects( $this->atLeastOnce() )
			->method( 'getContext' )
			->will( $this->returnValue( $context ) );

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertThatHookIsExcutable(
			$instance->getHandlersFor( 'OutputPageParserOutput' ),
			array( &$outputPage, $parserOutput )
		);
	}

	private function assertThatHookIsExcutable( \Closure $handler, $arguments ) {
		$this->assertInternalType(
			'boolean',
			call_user_func_array( $handler, $arguments )
		);
	}

}
