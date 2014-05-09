<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
include_once JPATH_COMPONENT.'/helpers/weblinks.php';

/**
 * View to edit a weblink.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 * @since       1.5
 */
class WeblinksViewWeblinkHtml extends JViewCms
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function render($tpl = null)
	{
		$model = $this->getModel();
		$this->state	= $model->getState();
		$this->item		= $model->getItem();
		$this->form		= $model->getForm();

		$this->addToolbar();
		return parent::render($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= WeblinksHelper::getActions($this->item->catid, 0);

		JToolbarHelper::title(JText::_('COM_WEBLINKS_MANAGER_WEBLINK'), 'weblinks.png');

		if($isNew)
		{
			$taskPrefix = 'create';
		}
		else
		{
			$taskPrefix = 'update';
		}

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_weblinks', 'core.create')))))
		{
			JToolbarHelper::apply($taskPrefix.'Edit.weblink');
			JToolbarHelper::save($taskPrefix.'Close.weblink');
		}
		if (!$checkedOut && (count($user->getAuthorisedCategories('com_weblinks', 'core.create')))){
			JToolbarHelper::save2new($taskPrefix.'New.weblink');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_weblinks', 'core.create')) > 0))
		{
			JToolbarHelper::save2copy($taskPrefix.'Copy.weblink');
		}
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('cancel.weblink');
		}
		else
		{
			JToolbarHelper::cancel('cancel.weblink', 'JTOOLBAR_CLOSE');
		}

		// contenthistory not supported in MVSC yet
		/*
		if ($this->state->params->get('save_history') && $user->authorise('core.edit'))
		{
			$itemId = $this->item->id;
			$typeAlias = 'com_weblinks.weblink';
			JToolbarHelper::versions($typeAlias, $itemId);
		}

		*/
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}

	// I had to add these methods because
	// 1. JViewCms doesn't extend JObject
	// 2. Layouts was causing an error because it was calling JForm
 	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 *
	 * @return  mixed    The value of the property.
	 *
	 * @since   11.1
	 *
	 * @see     getProperties()
	 */
	public function get($property, $default = null)
	{
		if (isset($this->$property))
		{
			return $this->$property;
		}
		return $default;
	}

	public function getForm()
	{
		return $this->form;
	}
}
