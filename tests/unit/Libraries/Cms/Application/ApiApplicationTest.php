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
use Joomla\CMS\Application\Exception\NotAcceptable;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\ApiRouter;
use Joomla\DI\Container;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Session\SessionInterface;
use Joomla\Test\TestHelper;
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
	const TEST_ACCEPT_HEADER = 'application/vnd.api+json';

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
		$_SERVER['HTTP_ACCEPT'] = self::TEST_ACCEPT_HEADER;
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

	/**
	 * Tests that the name is correctly loaded by the constructor
	 *
	 * @since   4.0.0
	 */
	public function testAddFormat()
	{
		$this->apiApplication->addFormatMap('application/graphql', 'graphql');

		$this->assertArrayHasKey(
			'application/graphql',
			TestHelper::getValue($this->apiApplication, 'formatMapper'),
			'The name of the application should be api'
		);
	}

	/**
	 * Tests the router. As a general rule in the CMS we don't test protected methods but as this covers
	 * all our content negotation and route matching special exceptions are being made.
	 *
	 * @since   4.0.0
	 */
	public function testRouteWithSuccessfulMatch()
	{
		// Various mocks required to set up the application
		$container = new Container;
		$mockEventDispatcher = $this->getMockBuilder(DispatcherInterface::class)->getMock();
		$dummyEvent = new Event('onBeforeApiRoute', [&$mockRouter, $this]);

		// Ensure we get a dummy event back for the onBeforeApiRoute
		// TODO: This is a bodge - we should be detecting for specific events!
		$mockEventDispatcher->method('dispatch')
			// ->with(['onBeforeApiRoute', $dummyEvent])
			->willReturn($dummyEvent);
		$mockSession = $this->getMockBuilder(SessionInterface::class)->getMock();

		$app = new ApiApplication(null, null, null, $container);
		$app->setDispatcher($mockEventDispatcher);
		$app->setSession($mockSession);
		Factory::$application = $app;

		$expectedComponent = 'com_foo';

		// Mock our API router
		$mockRouter = $this->getMockBuilder(ApiRouter::class)
			->setConstructorArgs([$app])
			->getMock();
		$mockRouter->method('parseApiRoute')
			->willReturn([
				'controller' => 'foobar',
				'task'       => 'foobar',
				'vars'       => [
					'format' => ['application/vnd.api+json'],
					'component' => $expectedComponent,
					'public' => true,
				],
				]
			);

		$container->set('ApiRouter', $mockRouter)
			->set(DispatcherInterface::class, $mockEventDispatcher);

		TestHelper::invoke($app, 'route');

		$this->assertEquals($expectedComponent, $app->input->get('option'));
	}

	/**
	 * Tests the router. As a general rule in the CMS we don't test protected methods but as this covers
	 * all our content negotation and route matching special exceptions are being made.
	 *
	 * @since   4.0.0
	 */
	public function testBadContentNegotation()
	{
		$this->expectException(NotAcceptable::class);
		$this->expectExceptionCode(406);

		// Various mocks required to set up the application
		$container = new Container();
		$mockEventDispatcher = $this->getMockBuilder(DispatcherInterface::class)->getMock();
		$dummyEvent = new Event('onBeforeApiRoute', [&$mockRouter, $this]);

		// Ensure we get a dummy event back for the onBeforeApiRoute
		// TODO: This is a bodge - we should be detecting for specific events!
		$mockEventDispatcher->method('dispatch')
			// ->with(['onBeforeApiRoute', $dummyEvent])
			->willReturn($dummyEvent);
		$mockSession = $this->getMockBuilder(SessionInterface::class)->getMock();

		$app = new ApiApplication(null, null, null, $container);
		$app->setDispatcher($mockEventDispatcher);
		$app->setSession($mockSession);
		Factory::$application = $app;

		$expectedComponent = 'com_foo';

		$mockRouter = $this->getMockBuilder(ApiRouter::class)
			->setConstructorArgs([$app])
			->getMock();

		$mockRouter->method('parseApiRoute')
			->willReturn([
				'controller' => 'foobar',
				'task'       => 'foobar',
				'vars'       => [
					'format' => ['application/graphql'],
					'component' => $expectedComponent,
					'public' => true,
				],
				]
			);

		$container->set('ApiRouter', $mockRouter)
			->set(DispatcherInterface::class, $mockEventDispatcher);

		TestHelper::invoke($app, 'route');
	}
}
