<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomlacms\Event;

defined('JPATH_PLATFORM') or die;

/**
 * CMS event class
 *
 * @package     Joomla.Libraries
 * @subpackage  Event
 * @since       3.4
 */
class Cms extends Dispatcher
{
	/**
	 * Stores the singleton instance of the dispatcher.
	 *
	 * @var    \Joomla\Event\Cms
	 * @since  11.3
	 */
	protected static $instance = null;

	/**
	 * Returns the global Dispatcher object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  \Joomla\Event\Dispatcher  The EventDispatcher object.
	 *
	 * @since   11.1
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new Cms;
		}

		return self::$instance;
	}
}
