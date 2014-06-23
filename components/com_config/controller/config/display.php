<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Display Controller for global configuration
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
*/
class ConfigControllerConfigDisplay extends JControllerDisplay
{
	/*
	 * Permission needed for the action. Defaults to most restrictive
	 *
	 * @var  string
	 * @since  3.4
	 */
	public $permission = 'core.admin';

	/**
	 * Method to get a view, initiating it if it does not already exist.
	 * This method assumes auto-loading format is $prefix . 'View' . $name . $type
	 * The
	 *
	 * @param   JModelCmsInterface  $model   The model to be injected
	 * @param   string              $prefix  Option prefix exp. com_content
	 * @param   string              $name    Name of the view folder exp. articles
	 * @param   string              $type    Name of the file exp. html = html.php
	 * @param   array               $config  An array of config options
	 *
	 * @throws  RuntimeException
	 * @return  JViewCms
	 */
	protected function getView(JModelCmsInterface $model, $prefix = null, $name = null, $type = null, $config = array())
	{
		$doc = JFactory::getDocument();

		$viewFormat = $doc->getType();
		// Initialise the view class.
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COMPONENT . '/view/' . $this->viewName . '/tmpl', 'normal');
		$viewClass  = 'ConfigView' . ucfirst($this->viewName) . ucfirst($viewFormat);
		$view = new $viewClass($model, $paths);

		// If in html view then we set the layout
		if ($viewFormat == 'html')
		{
			$layoutName   = $this->input->getWord('layout', 'default');
			$view->setLayout($layoutName);
		}

		// Push document object into the view.
		$view->document = JFactory::getDocument();

		// Load form and bind data
		$form = $model->getForm();

		// Set form and data to the view
		$view->form = &$form;
		$view->data = $model->getServiceData($this->input);

		return $view;
	}
}
