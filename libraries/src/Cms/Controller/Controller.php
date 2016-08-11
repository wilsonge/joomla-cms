<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Controller;

defined('JPATH_PLATFORM') or die;

use Serializable;
use LogicException;
use RuntimeException;
use JApplicationBase;

/**
 * Joomla Platform Controller Interface
 *
 * @since  12.1
 */
interface Controller extends Serializable
{
	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function execute();

	/**
	 * Get the application object.
	 *
	 * @return  JApplicationBase  The application object.
	 *
	 * @since   12.1
	 */
	public function getApplication();

	/**
	 * Get the input object.
	 *
	 * @return  \Joomla\Input\Input  The input object.
	 *
	 * @since   12.1
	 */
	public function getInput();
}
