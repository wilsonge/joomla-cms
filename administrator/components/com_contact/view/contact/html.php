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
 * View to edit a contact.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class ContactViewContactHtml extends JViewHtmlLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function render()
	{
		$data = $this->getData();
		$this->state = $data['state'];
		$this->item  = $data['item'];
		$this->form  = $data['form'];

		$this->addToolbar();

		return parent::render();
	}

	public function getData()
	{
		$model = $this->getModel();
		$data = array();
		$data['state'] = $model->getState();
		$data['item'] = $model->getItem();
		$data['form'] = $model->getForm();

		// If in modal set some fields to have readonly values
		if ($this->getLayout() == 'modal')
		{
			$data['form']->setFieldAttribute('language', 'readonly', 'true');
			$data['form']->setFieldAttribute('catid', 'readonly', 'true');
		}
	
		return $data;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		// Get the data
		$data   = $this->getData();

		// Get the user object and ID
		$user		= JFactory::getUser();
		$userId		= $user->id;

		// Assemble relevent item information
		$item	    = $data['item'];		
		$isNew		= ($item->id == 0);
		$checkedOut	= !($item->checked_out == 0 || $item->checked_out == $userId);

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= JHelperContent::getActions('com_contact', 'category', $item->catid);

		JToolbarHelper::title(JText::_('COM_CONTACT_MANAGER_CONTACT'), 'address contact');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($isNew && (count($user->getAuthorisedCategories('com_contact', 'core.create')) > 0))
			{
				JToolbarHelper::apply('contact.apply');
				JToolbarHelper::save('contact.save');
				JToolbarHelper::save2new('contact.save2new');
			}

			JToolbarHelper::cancel('contact.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $item->created_by == $userId))
				{
					JToolbarHelper::apply('contact.apply');
					JToolbarHelper::save('contact.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolbarHelper::save2new('contact.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('contact.save2copy');
			}

			$params = $data['state']->get('params');

			if ($params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_contact.contact', $item->id);
			}

			JToolbarHelper::cancel('contact.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_CONTACTS_CONTACTS_EDIT');
	}
}
