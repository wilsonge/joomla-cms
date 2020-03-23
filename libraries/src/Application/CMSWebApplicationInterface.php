<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Application;

\defined('JPATH_PLATFORM') or die;

use Joomla\Application\SessionAwareWebApplicationInterface;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Menu\AbstractMenu;

/**
 * Interface defining a Joomla! CMS Application class for web applications.
 *
 * @since  __DEPLOY_VERSION__
 */
interface CMSWebApplicationInterface extends SessionAwareWebApplicationInterface, CMSApplicationInterface
{
	/**
	 * Method to get the application document object.
	 *
	 * @return  Document  The document object
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDocument();

	/**
	 * Get the menu object.
	 *
	 * @param   string  $name     The application name for the menu
	 * @param   array   $options  An array of options to initialise the menu with
	 *
	 * @return  AbstractMenu|null  An AbstractMenu object or null if not set.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getMenu($name = null, $options = array());

	/**
	 * Gets a user state.
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  The user state or null.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getUserState($key, $default = null);

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable, for valid values see {@link InputFilter::clean()}. Optional.
	 *
	 * @return  mixed  The request user state.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none');

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  $key    The path of the state.
	 * @param   mixed   $value  The value of the variable.
	 *
	 * @return  mixed|void  The previous state, if one existed. Void otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setUserState($key, $value);
}
