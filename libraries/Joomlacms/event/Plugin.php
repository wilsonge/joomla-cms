<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomlacms\Event;

use Joomla\Event\Event;

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
	 * The result of triggering the plugin event
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $result = array();

	/**
	 * Forbid stopping the event propagation.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @throws  \BadMethodCallException
	 */
	public function stop()
	{
		throw new \BadMethodCallException('You are not allowed to stop a plugin event');
	}

	/**
	 * Allows someone to set a result. Each result is merged together to give a final
	 * overall return as to the success of all the listeners.
	 *
	 * @param  mixed  $result  The result to register
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @throws  \InvalidArgumentException
	 */
	public function setResult($result)
	{
		$this->result[] = $result;
	}

	/**
	 * Allows someone to get the result of the plugin events.
	 *
	 * @return  array  The result array
	 *
	 * @since   3.4
	 */
	public function getResults()
	{
		return $this->result;
	}
}