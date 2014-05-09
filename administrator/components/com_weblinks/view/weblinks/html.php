<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of weblinks.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 * @since       1.5
 */
class WeblinksViewWeblinksHtml extends JViewCms
{
	protected $items;

	protected $pagination;

	protected $state;


	public function render($tpl = null)
	{
		$model = $this->getModel();
		$this->state		= $model->getState();
		$this->items		= $model->getItems();
		$this->pagination	=$model->getPagination();

		WeblinksHelper::addSubmenu('weblinks');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		return parent::render($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/weblinks.php';

		$state	= $this->get('State');
		$canDo	= WeblinksHelper::getActions($state->get('filter.category_id'));
		$user	= JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_WEBLINKS_MANAGER_WEBLINKS'), 'weblinks.png');
		if (count($user->getAuthorisedCategories('com_weblinks', 'core.create')) > 0)
		{
			JToolbarHelper::addNew('add.weblink');
		}
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('edit.weblink');
		}
		if ($canDo->get('core.edit.state')) {

			JToolbarHelper::publish('statePublish.weblinks', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('stateUnpublish.weblinks', 'JTOOLBAR_UNPUBLISH', true);

			JToolbarHelper::archiveList('stateArchive.weblinks');
			JToolbarHelper::checkin('checkin.weblinks');
		}
		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'delete.weblinks', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('trash.weblinks');
		}

		// Add a batch button
		// Currently not supported in MVSC
		/*
		if ($user->authorise('core.create', 'com_weblinks') && $user->authorise('core.edit', 'com_weblinks') && $user->authorise('core.edit.state', 'com_weblinks'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		*/

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_weblinks');
		}

		JToolbarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS');

		JHtmlSidebar::setAction('index.php?option=com_weblinks&view=weblinks');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_weblinks'), 'value', 'text', $this->state->get('filter.category_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);

		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_TAG'),
		'filter_tag',
		JHtml::_('select.options', JHtml::_('tag.options', true, true), 'value', 'text', $this->state->get('filter.tag'))
		);

	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
