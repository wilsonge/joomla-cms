<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license	    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JHelperContenthistory.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Helper
 * @since       3.2
 */
class JHelperContenthistoryTest extends TestCaseDatabase
{
	/**
	 * @var    JHelperContenthistory
	 * @since  3.2
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new JHelperContenthistory;
		JFactory::$application = $this->getMockApplication();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   3.2
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_users', JPATH_TEST_DATABASE . '/jos_users.csv');
		$dataSet->addTable('jos_content', JPATH_TEST_DATABASE . '/jos_content.csv');
		$dataSet->addTable('jos_ucm_history', JPATH_TEST_DATABASE . '/jos_ucm_history.csv');

		return $dataSet;
	}
	/**
	 * Tests the deleteHistory() method
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function testDeleteHistory()
	{
		$this->markTestSkipped('Test not implemented.');
	}

	public function getHistoryData()
	{
		return array(
			array('com_content.article', 1, 2),
			array('com_content.article', 2, 2),
			array('com_weblinks.weblink', 1, 2),
		);
	}

	/**
	 * Tests the getHistory method
	 *
	 * @return  void
	 *
	 * @dataProvider getHistoryData
	 *
	 * @since   3.2
	 */
	public function getHistory($type, $id, $expectedNumber)
	{
		$this->assertEquals($expectedNumber, $this->object->getHistory(), 'There should be ' . $expectedNumber . ' objects in the #__ucm_history table for the type ' . $type . ' and id ' . $id);
	}

	/**
	 * Tests the store() method
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function store()
	{
		$this->markTestSkipped('Test not implemented.');
	}
}
