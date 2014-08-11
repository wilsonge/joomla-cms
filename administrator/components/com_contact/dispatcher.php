<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Component Dispatcher
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 */
class ContactDispatcher extends JComponentDispatcher
{
	/**
	 * @var		string	The default view.
	 * @since   1.6
	 */
	public $defaultView = 'contacts';

	private $activityMap = array();

	/**
	 * Sets the input, option, config and view variables for use elsewhere in the class.
	 *
	 * @param   string  $option  The application object
	 * @param   JInput  $input   The input class
	 * @param   array   $config  A config array
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($option, JInput $input = null, $config = array())
	{
		parent::__construct($option, $input, $config);

		$this->activityMap = array(
			'add' => 'create',
			'apply' => 'update',
			'save' => 'update',
			'save2new' => 'update',
			'edit' => 'display',
			'publish' => 'updatestatelist',
			'unpublish' => 'updatestatelist',
			'trash' => 'updatestatelist',
			'archive' => 'updatestatelist'
		);
	}

	/**
	 * Method to get the controller information from the URL
	 * Defaults to the base controllers.
	 *      $tasks[CONTROLLER_PREFIX] is the location of the controller which defaults to the core libraries (referenced as 'j'
	 *      and then the named folder within the component entry point file.
	 *      $tasks[CONTROLLER_ACTIVITY] is the name of the controller file,
	 *      $tasks[CONTROLLER_VIEW_FOLDER] is the name of the folder found in the component controller folder for controllers
	 *      not prefixed with J.
	 *
	 * @return   array  The tasks in the form listed above
	 */
	public function getTasks()
	{
		$tasks = parent::getTasks();

		// If we have a legacy style tasks with only 2 options it means we're missing
		// the component element so add that in. Also we need to rearrange the order
		// of the tasks
		if (count($tasks) == 2)
		{
			$newTasks = array (
				'Contact',
				$tasks[1],
				$tasks[0]
			);
		}
		else
		{
			$newTasks = $tasks;
		}

		return $newTasks;
	}

	/**
	 * Gets the default controller activity
	 *
	 * @return  string  The default component activity
	 */
	protected function getControllerActivity()
	{
		$activity = parent::getControllerActivity();

		$activity = $this->mapActivity($activity);

		return $activity;
	}

	/**
	 * Maps irregularly named activity's to the correct controller activity name
	 *
	 * @return  string  The activity
	 */	
	private function mapActivity($activity)
	{
		if (isset($this->activityMap[strtolower($activity)]))
		{
			$activity = ucfirst($this->activityMap[strtolower($activity)]);
		}

		return $activity;
	}
}
