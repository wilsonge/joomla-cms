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
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       3.4
 */
class ContactControllerContactsFeatured extends JControllerCms
{
	/**
	 * @var    integer  The value if featured.
	 * @since  3.4
	 */
	protected $value = 1;

	/**
	 * Method to toggle the featured setting of a list of contacts.
	 *
	 * @return  void
	 * @since   3.4
	 */
	public function execute()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user   = JFactory::getUser();
		$ids    = $this->input->get('cid', array(), 'array');

		// Get the model.
		$model  = $this->getModel('Contact', 'Contact');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			$item = $model->getItem($id);

			if (!$user->authorise('core.edit.state', 'com_contact.category.' . (int) $item->catid))
			{
				// Prune items that the user has no permissions to change.
				unset($ids[$i]);

				$this->app->setHeader('status', '403 Insufficient Permissions');
				$this->app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
		{
			// @todo correct the message with the 500 error
			$this->app->setHeader('status', '500 No Content');
			$this->app->enqueueMessage(JText::_('COM_CONTACT_NO_ITEM_SELECTED'));
			$this->setRedirect('index.php?option=com_contact&view=contacts');

			return false;
		}
		else
		{
			// Publish the items.
			try
			{
				// @todo convert the model to use Exceptions
				$model->featured($ids, $this->value);
			}
			catch (Exception $e)
			{
				$this->app->setHeader('status', $e->getCode());
				$this->app->enqueueMessage($e->getMessage());
				$this->setRedirect('index.php?option=com_contact&view=contacts');

				return false;
			}
		}

		$this->setRedirect('index.php?option=com_contact&view=contacts');

		return true;
	}
}
