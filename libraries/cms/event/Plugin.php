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
	 * The result of triggering the plugin event
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $result = true;

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
	 * @param  boolean  $result  The result to register
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @throws  \InvalidArgumentException
	 */
	public function setResult($result)
	{
		if (!is_bool($result))
		{
			throw new \InvalidArgumentException('The result must be a boolean');
		}

		$this->result = ($result && $this->result);
	}

	/**
	 * Allows someone to get the result.
	 *
	 * @return  boolean  The result
	 *
	 * @since   3.4
	 */
	public function getResult()
	{
		return $this->result;
	}
}