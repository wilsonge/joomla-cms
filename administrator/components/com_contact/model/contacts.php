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
 * Methods supporting a list of contact records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 */
class ContactModelContacts extends JModelAdministrator
{
	/**
	 * Method to get a table object. The contacts table object is the singular contact.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTableInterface  A JTableInterface object
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function getTable($name = null, $prefix = null, $options = array())
	{
		return parent::getTable('Contact', $prefix, $options);
	}

	/**
	 * Public constructor
	 *
	 * @param  JRegistry         $state       The state for the model
	 * @param  JDatabaseDriver   $db          The database object
	 * @param  JEventDispatcher  $dispatcher  The dispatcher object
	 * @param  array             $config      Array of config variables
	 *
	 * @since  3.4
	 */
	public function __construct(JRegistry $state = null, JDatabaseDriver $db = null, JEventDispatcher $dispatcher = null, $config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				array('id', 'a.id'),
				array('name', 'a.name'),
				array('alias', 'a.alias'),
				array('checked_out', 'a.checked_out'),
				array('checked_out_time', 'a.checked_out_time'),
				array('catid', 'a.catid', 'category_title'),
				array('user_id', 'a.user_id'),
				array('published', 'a.published'),
				array('access', 'a.access', 'access_level'),
				array('created', 'a.created'),
				array('created_by', 'a.created_by'),
				array('ordering', 'a.ordering'),
				array('featured', 'a.featured'),
				array('language', 'a.language'),
				array('publish_up', 'a.publish_up'),
				array('publish_down', 'a.publish_down'),
				array('linked_user', 'ul.name')
			);

			$assoc = JLanguageAssociations::isEnabled();

			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($state, $db, $dispatcher, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$context = $this->getContext();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($context . '.filter.search', 'filter_search');
		$this->state->set('filter.search', $search);

		$access = $this->getUserStateFromRequest($context . '.filter.access', 'filter_access', 0, 'int');
		$this->state->set('filter.access', $access);

		$published = $this->getUserStateFromRequest($context . '.filter.published', 'filter_published', '');
		$this->state->set('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($context . '.filter.category_id', 'filter_category_id');
		$this->state->set('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($context . '.filter.language', 'filter_language', '');
		$this->state->set('filter.language', $language);

		// force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->state->set('filter.language', $forcedLanguage);
			$this->state->set('filter.forcedLanguage', $forcedLanguage);
		}

		$tag = $this->getUserStateFromRequest($context . '.filter.tag', 'filter_tag', '');
		$this->state->set('filter.tag', $tag);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 *
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getStateVar('filter.search');
		$id .= ':' . $this->getStateVar('filter.access');
		$id .= ':' . $this->getStateVar('filter.published');
		$id .= ':' . $this->getStateVar('filter.category_id');
		$id .= ':' . $this->getStateVar('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDb();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select the required fields from the table.
		$query->select(
			$this->getStateVar(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid, a.user_id' .
					', a.published, a.access, a.created, a.created_by, a.ordering, a.featured, a.language' .
					', a.publish_up, a.publish_down'
			)
		);
		$query->from('#__contact_details AS a');

		// Join over the users for the linked user.
		$query->select('ul.name AS linked_user')
			->join('LEFT', '#__users AS ul ON ul.id=a.user_id');

		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.title AS category_title')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Join over the associations.
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_contact.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}

		// Filter by access level.
		if ($access = $this->getStateVar('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by published state
		$published = $this->getStateVar('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Filter by a single or group of categories.
		$categoryId = $this->getStateVar('filter.category_id');

		if (is_numeric($categoryId))
		{
			$query->where('a.catid = ' . (int) $categoryId);
		}
		elseif (is_array($categoryId))
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN (' . $categoryId . ')');
		}

		// Filter by search in name.
		$search = $this->getStateVar('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(uc.name LIKE ' . $search . ' OR uc.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getStateVar('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter by a single tag.
		$tagId = $this->getStateVar('filter.tag');
		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_contact.contact')
				);
		}

		// Add the list ordering clause.
		$orderCol = $this->getStateVar('list.ordering', 'a.name');
		$orderDirn = $this->getStateVar('list.direction', 'asc');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
		}
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}
