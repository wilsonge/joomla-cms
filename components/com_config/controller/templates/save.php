<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Save Controller for global configuration
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
*/
class ConfigControllerTemplatesSave extends JControllerUpdate
{
	/**
	 * Method to save global configuration.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 */
	public function execute()
	{
		// Check for request forgeries.
		if (!JSession::checkToken())
		{
			$this->app->enqueueMessage(JText::_('JINVALID_TOKEN'));
			$this->setRedirect('index.php');

			return false;
		}

		// Check if the user is authorized to do this.
		if (!JFactory::getUser()->authorise('core.admin'))
		{
			$this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect('index.php');

			return false;
		}

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');

		// Access back-end com_templates
		JLoader::register('TemplatesControllerStyle', JPATH_ADMINISTRATOR . '/components/com_templates/controllers/style.php');
		JLoader::register('TemplatesModelStyle', JPATH_ADMINISTRATOR . '/components/com_templates/models/style.php');
		JLoader::register('TemplatesTableStyle', JPATH_ADMINISTRATOR . '/components/com_templates/tables/style.php');
		$controllerClass = new TemplatesControllerStyle;

		// Set back-end required params
		$this->doc->setType('json');
		$this->input->set('id', $this->app->getTemplate('template')->id);

		// Execute back-end controller
		$return = $controllerClass->save();

		// Reset params back after requesting from service
		$this->doc->setType('html');

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('JERROR_SAVE_FAILED');

			$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.templates', false), $message, 'error');

			return false;
		}

		// Set the success message.
		$message = JText::_('COM_CONFIG_SAVE_SUCCESS');

		// Redirect back to com_config display
		$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.templates', false), $message);

		return true;
	}
}
