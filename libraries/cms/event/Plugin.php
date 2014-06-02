<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

defined('JPATH_PLATFORM') or die;

/**
 * CMS event class
 *
 * @package     Joomla.Libraries
 * @subpackage  Event
 * @since       3.4
 */
class Plugin extends Event
{
	/**
	 * Forbid stopping the event propagation.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \BadMethodCallException
	 */
	public function stop()
	{
		throw new \BadMethodCallException('You are not allowed to stop a plugin event');
	}
}