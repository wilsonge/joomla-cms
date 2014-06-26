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
class ConfigControllerComponentSave extends JControllerUpdate
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

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');

		try
		{
			$model = $this->getModel('Config', 'Component');
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		$form   = $model->getForm();
		$data   = $this->input->get('jform', array(), 'array');
		$id     = $this->input->getInt('id');
		$option = $this->input->get('component');

		// Check if the user is authorized to do this.
		if (!JFactory::getUser()->authorise('core.admin', $option))
		{
			$this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect('index.php');

			return false;
		}

		$returnUri = $this->input->post->get('return', null, 'base64');

		$redirect = '';

		if (!empty($returnUri))
		{
			$redirect = '&return=' . urlencode($returnUri);
		}

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
			$this->setRedirect(JRoute::_('index.php?option=com_config&view=component&component=' . $option . $redirect, false));

			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id'     => $id,
			'option' => $option
		);

		try
		{
			$model->save($data);
		}
		catch (RuntimeException $e)
		{
			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->app->enqueueMessage(JText::sprintf('JERROR_SAVE_FAILED', $e->getMessage()), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_config&view=component&component=' . $option . $redirect, false));

			return false;
		}

		// Set the redirect based on the task.
		switch ($this->options[3])
		{
			case 'apply':
				$this->app->enqueueMessage(JText::_('COM_CONFIG_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_config&view=component&component=' . $option . $redirect, false));

				break;

			case 'save':
			default:
				$redirect = 'index.php?option=' . $option;

				if (!empty($returnUri))
				{
					$redirect = base64_decode($returnUri);
				}

				$this->setRedirect(JRoute::_($redirect, false));

				break;
		}

		return true;
	}
}
