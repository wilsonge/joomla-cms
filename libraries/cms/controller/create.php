<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Libraries
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Base Display Controller
 *
 * @package     Joomla.Libraries
 * @subpackage  controller
 * @since       3.2
*/
class JControllerCreate extends JControllerCmsbase
{
	const CONTROLLER_PREFIX = 0;
	const CONTROLLER_ACTIVITY = 1;
	const CONTROLLER_VIEW_FOLDER = 2;
	const CONTROLLER_OPTION = 3;

	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the record can be added, a error object if not.
	 *
	 * @since   12.2
	 */
	/*
	 * Prefix for the view and model classes
	 *
	 * @var  string
	 */
	public $prefix;

	/*
	 * Option to send to the model.
	*
	* @var  array
	*/
	public $options;

	/**
	 * @return  mixed  A rendered view or true
	 *
	 * @since   3.2
	 */
	public function execute()
	{
		$context = $this->input->getWord('option', 'com_content') . '.edit.' .  $this->options[self::CONTROLLER_VIEW_FOLDER];

		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Access check.
		if (!$this->allowAdd())
		{
			// Set the internal error and also the redirect error.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			$this->app->enqueueMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->input->get('option') . '&controller=j.display.' . $options[self::CONTROLLER_PREFIX],
					false
				)
			);

			return false;
		}

		// Clear the record edit information from the session if this is not a copy.
		if (empty($this->options[self::CONTROLLER_OPTION]) || $this->options[self::CONTROLLER_OPTION] != 'copy')
		{
			$this->app->setUserState($context . '.data', null);
		}

		// Redirect to the edit screen.
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->input->getWord('option') . '&view='
				. $this->options[self::CONTROLLER_VIEW_FOLDER] . '&layout=edit', false
			)
		);

		$this->app->redirect('index.php?option=' . $this->input->getWord('option') . '&view='
				. $this->options[self::CONTROLLER_VIEW_FOLDER] . '&layout=edit');

		return true;
	}
	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		return ($user->authorise('core.create', $this->input->getWord('option')) || count($user->getAuthorisedCategories($this->input->getWord('option'), 'core.create')));
	}
}
