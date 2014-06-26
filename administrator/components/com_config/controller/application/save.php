<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Save Controller for global configuration
 *
 * @package     Joomla.Administrator
 * @subpackage  com_config
 * @since       3.2
 */
class ConfigControllerApplicationSave extends JControllerUpdate
{
	/**
	 * Method to save global configuration.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
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
			$model = $this->getModel('Config', 'Application');
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		$data  = $this->input->post->get('jform', array(), 'array');

		// Complete data array if needed
		$oldData = $model->getData();
		$data = array_replace($oldData, $data);

		// Get request type
		$saveFormat = JFactory::getDocument()->getType();

		// Handle service requests
		if ($saveFormat == 'json')
		{
			// @todo NO!!!
			return $model->save($data);
		}

		// Must load after serving service-requests
		$form = $model->getForm();

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
			$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.application', false));

			return false;
		}

		// Attempt to save the configuration.
		$data	= $return;
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			/*
			 * The save method enqueued all messages for us, so we just need to redirect back.
			 */

			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->setRedirect(JRoute::_('index.php?option=com_config&controller=config.display.application', false));

			return false;
		}

		// Set the success message.
		$this->app->enqueueMessage(JText::_('COM_CONFIG_SAVE_SUCCESS'));

		// Set the redirect based on the task.
		switch ($this->options[3])
		{
			case 'apply':
				$this->app->redirect(JRoute::_('index.php?option=com_config', false));
				break;

			case 'save':
			default:
				$this->app->redirect(JRoute::_('index.php', false));
				break;
		}
	}
}
