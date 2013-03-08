<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       2.5.4
 */

defined('_JEXEC') or die;

/**
 * The Joomla! update controller for the Update view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 * @since       2.5.10
 */
class JoomlaupdateControllerCompatibility extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean  $cachable   If true, the view output will be cached
	 * @param	array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 *
	 * @since	2.5.10
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get the document object.
		$app		= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$input 		= $app->input;

		// Set the default view name and format from the Request.
		$vName		= $input->getCmd('view', 'compatibility');
		$vFormat	= $document->getType();
		$lName		= $input->getCmd('layout', 'default');
		$extension 	= $input->getCmd('extension');
		
		//check if extension are not empty
		if (is_null($extension)) {
			$app->redirect('index.php?option=com_joomlaupdate', 'COM_JOOMLAUPDATE_COMPATIBILITY_EMPTY_EXTENSION_VALUE');
		}
		
		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat)) {
			// Get the model for the view.
			$model = $this->getModel($vName);
			
			//validate if extension exists
			if (!$model->extensionExists($extension)) {
				$app->redirect('index.php?option=com_joomlaupdate', 'COM_JOOMLAUPDATE_COMPATIBILITY_INVALID_EXTENSION_VALUE', 'warning');
			}
			// Push the model into the view (as default).
			$view->setModel($model, true);

			$view->display();
		}

		return $this;
	}
}