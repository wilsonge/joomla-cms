<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/table/extension.php';

/**
 * Test class for JTableExtension.
 * Generated by PHPUnit on 2011-12-06 at 03:27:17.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Table
 * @since       11.1
 */
class JTableExtensionTest extends TestCaseDatabase
{
	/**
	 * @var  JTableExtension
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		// Get the mocks
		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		$mockApp = $this->getMockCmsApp();
		$mockApp->expects($this->any())
			->method('getDispatcher')
			->willReturn($this->getMockDispatcher());
		JFactory::$application = $mockApp;

		$this->object = new JTableExtension(self::$driver);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   11.4
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * Tests JTableExtension::check
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCheck()
	{
		$table = $this->object;

		$this->assertThat(
			$table->check(),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' Checking an empty table should fail.'
		);

		$table->name = 'com_content';
		$table->element = 'com_content';
		$this->assertThat(
			$table->check(),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The check function should complete without issue.'
		);
	}

	/**
	 * Tests JTableExtension::find
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testFind()
	{
		$table = $this->object;

		$this->assertThat(
			$table->find(array('name' => 'com_content')),
			$this->equalTo('22'),
			'Line: ' . __LINE__ . ' The find method should return the extension_id of the specified extension.'
		);
	}

	/**
	 * Tests JTableExtension::publish
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testPublish()
	{
		$table = $this->object;

		// Test with pk's in an array
		$pks = array('21', '23');
		$this->assertTrue($table->publish($pks, '0'), 'Line: ' . __LINE__ . ' Publish with an array of pks should work');
		$table->load('21');
		$this->assertEquals('0', $table->enabled, 'Line: ' . __LINE__ . ' Id 21 should be unpublished');
		$table->reset();
		$table->load('23');
		$this->assertEquals('0', $table->enabled, 'Line: ' . __LINE__ . ' Id 23 should be unpublished');
		$table->reset();

		// Test with a single pk
		$this->assertTrue($table->publish('22', '1'), 'Line: ' . __LINE__ . ' Publish with a single pk should work');
		$table->load('22');
		$this->assertEquals('1', $table->enabled, 'Line: ' . __LINE__ . ' Id 32 should be published');
	}
}
