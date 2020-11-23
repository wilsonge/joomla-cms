<?php
/**
 * @package    Joomla.API
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Libraries\Cms\Application;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\ApiApplication;
use Joomla\Tests\Unit\UnitTestCase;

/**
 * Joomla! API Application class
 *
 * @since  4.0.0
 */
class ApiApplicationTest extends UnitTestCase
{
	/**
	 * Value for test host.
	 *
	 * @var    string
	 * @since  3.2
	 */
	const TEST_HTTP_HOST = 'mydomain.com';

	/**
	 * Value for test user agent.
	 *
	 * @var    string
	 * @since  3.2
	 */
	const TEST_USER_AGENT = 'Mozilla/5.0';

	/**
	 * Value for test user agent.
	 *
	 * @var    string
	 * @since  3.2
	 */
	const TEST_REQUEST_URI = '/index.php';

	/**
	 * Maps extension types to their
	 *
	 * @var    ApiApplication
	 * @since  4.0.0
	 */
	protected $apiApplication;

	/**
	 * Backup of the SERVER superglobal
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $backupServer;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function setUp(): void
	{
		$this->backupServer = $_SERVER;

		$_SERVER['HTTP_HOST'] = self::TEST_HTTP_HOST;
		$_SERVER['HTTP_USER_AGENT'] = self::TEST_USER_AGENT;
		$_SERVER['REQUEST_URI'] = self::TEST_REQUEST_URI;
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$this->apiApplication = new ApiApplication;
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.2
	 */
	protected function tearDown(): void
	{
		$_SERVER = $this->backupServer;
		unset($this->backupServer, $this->apiApplication);

		parent::tearDown();
	}

	/**
	 * Tests that the name is correctly loaded by the constructor
	 *
	 * @since   4.0.0
	 */
	public function testApiApplicationNames()
	{
		$this->assertEquals(
			'api',
			$this->apiApplication->getName(),
			'The name of the application should be api'
		);

		$this->assertTrue(
			$this->apiApplication->isClient('api'),
			'The isClient method the application should be api'
		);
	}
}
