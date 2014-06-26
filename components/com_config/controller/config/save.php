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
 * Save Controller for global configuration
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
*/
class ConfigControllerConfigSave extends JControllerUpdate
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

		try
		{
			$model = $this->getModel('Config', 'Config');
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		$form  = $model->getForm();
		$data  = $this->input->post->get('jform', array(), 'array');

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			/*
			 * The validate method enqueued all messages for us, so we just need to redirect back.
			 */

			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.config', false));

			return false;
		}

		// Attempt to save the configuration.
		$data = $return;
		$this->input->post->set('jform', $data);

		// Access back-end com_config
		JLoader::registerPrefix('Config', JPATH_ADMINISTRATOR . '/components/com_config');
		$config = array (
			'option' => 'com_config',
			'view' => 'Application'
		);
		$saveClass = new ConfigControllerApplicationSave($config, $this->input);

		// Set back-end required params
		$this->doc->setType('json');

		// Execute back-end controller
		$return = $saveClass->execute();

		// Reset params back after requesting from service
		$this->doc->setType('html');

		// Check the return value.
		if ($return === false)
		{
			/*
			 * The save method enqueued all messages for us, so we just need to redirect back.
			 */

			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.config', false));

			return false;
		}

		// Redirect back to com_config display
		$this->app->enqueueMessage(JText::_('COM_CONFIG_SAVE_SUCCESS'));
		$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.config', false));

		return true;
	}
}
