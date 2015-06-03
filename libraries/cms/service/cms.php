<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

defined('JPATH_PLATFORM') or die;

/**
 * The CMS Service Provider - it provides a nasty implementation of a service
 * provider in 3.x but allows people to use DIC in Joomla 3 and a future version 4
 *
 * @since  3.5
 */
class JServiceCms implements ServiceProviderInterface
{
	/**
	 * The application object.
	 *
	 * @var    JApplicationWeb
	 * @since  3.5
	 */
	private $app;

	/**
	 * Public constructor.
	 *
	 * @param   JApplicationWeb  $app  The application object.
	 *
	 * @since   3.5
	 */
	public function __construct(JApplicationWeb $app)
	{
		$this->app = $app;
	}

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 *
	 * @since   3.5
	 */
	public function register(Container $container)
	{
		// Document
		$container->alias('document', 'JDocumentHtml')
			->set('JDocumentHtml', array('JFactory', 'getDocument'), false, true);

		// User
		$container->alias('user', 'JUser')
			->set('JUser', array('JFactory', 'getUser'), false, true);

		// Database
		$container->alias('database', 'JDatabase')
			->set('JDatabase', array('JFactory', 'getDbo'), false, true);

		// Note $this is only allowed in anonymous functions since PHP 5.4 so we'll just inject it.
		$app = $this->app;

		// Input
		$container->alias('input', 'JInput')
			->set('JInput', function () use($app) {
				return $app->input;
			}, false, true);

		/**
		 * Session - trying to access this via JFactory gives us an error because the method
		 * expects a options array to be injected on first call. We insert a Container object
		 * and horrible things happen. JApplicationWeb->getSession proxies to the same object
		 * but takes no param in it's method so we'll use this class.
		 */
		$container->alias('session', 'JSession')
			->set('JSession', function () use($app) {
				return $app->getSession();
			}, false, true);
	}
}
