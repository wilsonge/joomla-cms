<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Session
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

include_once __DIR__ . '/stubs/JSessionStorageArray.php';

use Joomla\Input\Input;
use Joomla\Session\Session;

/**
 * Test class for JSession.
 *
 * @since  3.4
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class JSessionTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
     * @since  3.4
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$application = $this->getMockCmsApp();

		$this->input = new Input;

		$this->session = new Session(
			$this->input,
			new JSessionStorageArray(md5('PHPSESSID')),
			null,
			array(
				'expire'    => 20,
				'force_ssl' => true,
				'name'      => 'PHPSESSID',
				'security'  => 'security'
			)
		);

		JFactory::$application->expects($this->any())
			->method('getSession')
			->willReturn($this->session);
	}

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     * @since  3.4
     */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Test checkToken
	 *
	 * @covers  JSession::checkToken
     * @since   3.4
	 */
	public function testCheckToken()
	{
		$formToken = JSession::getFormToken(true);

		JFactory::$application->input->post->set($formToken, $this->session->getToken());
		$this->input->post->set($formToken, $this->session->getToken());

		$this->assertTrue(JSession::checkToken(), 'Token is valid.');
	}

	/**
	 * Test getFormToken
	 *
	 * @covers  JSession::getFormToken
     * @since   3.4
	 */
	public function testGetFormToken()
	{
		$this->assertSame(32, strlen(JSession::getFormToken(false)), 'Validate form token length.');
	}
}
