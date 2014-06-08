<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license	    GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_TESTS . '/suites/libraries/cms/model/stubs/ModelMock.php';

/**
 * Test class for JViewCmshtml.
 *
 * @package     Joomla.UnitTest
 * @subpackage  View
 * @since       3.4
 */
class JViewCmshtmlTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Object under test
	 *
	 * @var    JViewCmshtml
	 * @since  3.4
	 */
	protected $object;

	/**
	 * Sets up the object and passes in a mock model
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	protected function setUp()
	{
		$modelMock = JModelCmsMock::create($this);
		$this->object = new JViewCmshtml($modelMock);
	}

	/**
	 * Tests the getRenderer method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @covers  JViewCmshtml::getRenderer
	 */
	public function testGetRenderer()
	{
		$this->assertInstanceOf(
			'Joomla\\Renderer\\RendererInterface',
			$this->object->getRenderer()
		);
	}

	/**
	 * Tests the render method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @covers  JViewCmshtml::render
	 */
	public function testRender()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Tests the setRenderer method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @covers  JViewCmshtml::setRenderer
	 */
	public function testSetRenderer()
	{
		// We should create a mock renderer object to implement this test
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
