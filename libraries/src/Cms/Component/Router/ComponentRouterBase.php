<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Component
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Component\Router;

defined('JPATH_PLATFORM') or die;

use Joomla\Cms\Application\ApplicationCms;
use Joomla\Cms\Menu\Menu;
use JFactory;

/**
 * Base component routing class
 *
 * @since  3.3
 */
abstract class ComponentRouterBase implements ComponentRouterInterface
{
	/**
	 * Application object to use in the router
	 *
	 * @var    ApplicationCms
	 * @since  3.4
	 */
	public $app;

	/**
	 * Menu object to use in the router
	 *
	 * @var    Menu
	 * @since  3.4
	 */
	public $menu;

	/**
	 * Class constructor.
	 *
	 * @param   ApplicationCms  $app   Application-object that the router should use
	 * @param   Menu            $menu  Menu-object that the router should use
	 *
	 * @since   3.4
	 */
	public function __construct($app = null, $menu = null)
	{
		if ($app)
		{
			$this->app = $app;
		}
		else
		{
			$this->app = JFactory::getApplication('site');
		}

		if ($menu)
		{
			$this->menu = $menu;
		}
		else
		{
			$this->menu = $this->app->getMenu();
		}
	}

	/**
	 * Generic method to preprocess a URL
	 *
	 * @param   array  $query  An associative array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function preprocess($query)
	{
		return $query;
	}
}
