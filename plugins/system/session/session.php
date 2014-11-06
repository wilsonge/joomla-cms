<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.session
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Joomla! Session System Plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.session
 * @since       3.4
 */
class PlgSystemSession extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  3.4
	 */
	protected $app;

	/**
	 * This event is triggered after the framework has loaded and the application initialise method has been called.
	 *
	 * @return	void
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function onAfterInitialise()
	{
		$user = JFactory::getUser();

		if ($user->guest)
		{
			return;
		}

		// Make sure, both plugins are activated or return.
		if (JPluginHelper::isEnabled('system', 'session') && !JPluginHelper::isEnabled('user', 'session'))
		{
			return;
		}

		// Read the update flag name to check for.
		$plg_params = new Registry(JPluginHelper::getPlugin('user', 'session')->params);
		$flag       = $plg_params->get('session_update_flag_name', 'refresh');

		// Get session information.
		$session            = JFactory::getSession();
		$session_id         = $session->getId();
		$session_handler    = $this->app->getCfg('session_handler');
		$supported_handlers = array(
			'database',
			// 'memcache',
			// 'memcached',
			'xcache'
		);

		// Our tweak currently works for selected session storage handlers only.
		if (in_array($session_handler, $supported_handlers))
		{
			if ($session->get("session.{$flag}", null) == true)
			{
				$user->groups = JUserHelper::getUserGroups($user->id);
				$user->getAuthorisedGroups();
				$user->getAuthorisedViewLevels();

				// Load session data by id.
				$handler = JSessionStorage::getInstance($session_handler);

				if ($db_session = $handler->read($session_id))
				{
					// Get the session data.
					$session_helper = new JSessionHelper;
					$db_session     = $session_helper->unserialize($db_session);

					// Populate helper vars.
					$sess_namespace = current(array_keys($db_session));
					$sess_data      = current(array_values($db_session));

					// Replace session user data by updated logged in user data to.
					$sess_data['user'] = & $user;

					// Store updated session data.
					if (false === ($written = $handler->write($session_id, $sess_namespace . '|' . serialize($sess_data))))
					{
						throw new RuntimeException(JText::_('PLG_SYSTEM_SESSION_ERROR_STORE_FAIL'), 500);
					}

					if (JDEBUG)
					{
						$this->app->enqueueMessage(JText::sprintf('PLG_SYSTEM_SESSION_ERROR_STORE_SUCCESS_DEBUG', ucfirst($session_handler)), 'notice');
					}
				}
				else
				{
					if (JDEBUG)
					{
						$this->app->enqueueMessage(JText::sprintf('PLG_SYSTEM_SESSION_ERROR_STORE_FAIL_DEBUG', ucfirst($session_handler)), 'error');
					}
				}

				// Unset refresh-flag.
				$session->set('session.refresh', null);
			}
		}
		else
		{
			// State the incompatibility so admins might consider to change the selected session handler.
			if (JDEBUG)
			{
				$this->app->enqueueMessage(JText::sprintf('PLG_SYSTEM_SESSION_ERROR_UNSUPPORTED_HANDLER', ucfirst($session_handler)), 'notice');
			}
		}
	}
}
