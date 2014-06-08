<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Libraries
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for controllers
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
*/
class ConfigControllerHelper
{
	/**
	 * Method to parse a controller from a url
	 * Defaults to the base controllers and passes an array of options. See the parseTasks function
	 * for more information on the options passed
	 *
	 * @param   JApplicationCms  $app  An application object
	 *
	 * @return  JController  A JController object
	 *
	 * @since   3.2
	 * @see     ConfigControllerHelper->parseTasks($app);
	 */
	public function parseController($app)
	{
		$tasks = $this->parseTasks($app);

		if (empty($tasks[0]) || $tasks[0] == 'Config')
		{
			$location = 'Config';
		}
		else
		{
			$location = ucfirst(strtolower($tasks[0]));
		}

		if (empty($tasks[1]))
		{
			$activity = 'Display';
		}
		else
		{
			$activity = ucfirst(strtolower($tasks[1]));
		}

		$view = '';

		if (!empty($tasks[2]))
		{
			$view = ucfirst(strtolower($tasks[2]));
		}

		// Some special handling for com_config administrator
		$option = $app->input->get('option');

		if ($app->isAdmin() && $option == 'com_config')
		{
			$component = $app->input->get('component');

			if (!empty($component))
			{
				$view = 'Component';
			}
			elseif ($option == 'com_config')
			{
				$view = 'Application';
			}
		}

		$controllerName = $location . 'Controller' . $view . $activity;

		if (!class_exists($controllerName))
		{
			return false;
		}

		$controller = new $controllerName;

		// Add the options
		$controller = $this->parseController($controller, $app);

		return $controller;
	}

	/**
	 * Method to parse a controller from a url
	 * Adds the options from an application object to a given controller. See the parseTasks
	 * function for more information on the options passed
	 *
	 * @param   JController      $controller  The controller object
	 * @param   JApplicationBase  $app         An application object
	 *
	 * @return  JController  The JController object with the attached options array
	 *
	 * @since   3.4
	 * @see     ConfigControllerHelper->parseTasks($app);
	 */
	public function parseOptions($controller, $app)
	{
		$tasks = $this->parseTasks($app);
		$controller->options = array();
		$controller->options = $tasks;

		return $controller;
	}

	/**
	 * An array of options.
	 * $options[0] is the location of the controller which defaults to the core libraries (referenced as 'j'
	 * and then the named folder within the component entry point file.
	 * $options[1] is the name of the controller file,
	 * $options[2] is the name of the folder found in the component controller folder for controllers
	 * not prefixed with Config.
	 * Additional options maybe added to parameterise the controller.
	 *
	 * @param   JApplicationBase  $app         An application object
	 *
	 * @return  array  An array of tasks.
	 *
	 * @since   3.4
	 */
	protected function parseTasks($app)
	{
		$tasks = array();

		if ($task = $app->input->get('task'))
		{
			// Toolbar expects old style but we are using new style
			// Remove when toolbar can handle either directly
			if (strpos($task, '/') !== false)
			{
				$tasks = explode('/', $task);
			}
			else
			{
				$tasks = explode('.', $task);
			}
		}
		elseif ($controllerTask = $app->input->get('controller'))
		{
			// Temporary solution
			if (strpos($controllerTask, '/') !== false)
			{
				$tasks = explode('/', $controllerTask);
			}
			else
			{
				$tasks = explode('.', $controllerTask);
			}
		}

		return $tasks;
	}
}
