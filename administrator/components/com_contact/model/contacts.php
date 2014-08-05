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
	 * List of filter classes.
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $filters = array('access', 'category', 'language', 'published', 'tags');

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
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'user_id', 'a.user_id',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'ul.name', 'linked_user',
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

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->contentType .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->contentType . '.filter.search', 'filter_search');
		$this->state->set('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->contentType . '.filter.access', 'filter_access', 0, 'int');
		$this->state->set('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->contentType . '.filter.published', 'filter_published', '');
		$this->state->set('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->contentType . '.filter.category_id', 'filter_category_id');
		$this->state->set('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->contentType . '.filter.language', 'filter_language', '');
		$this->state->set('filter.language', $language);

		// force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->state->set('filter.language', $forcedLanguage);
			$this->state->set('filter.forcedLanguage', $forcedLanguage);
		}

		$tag = $this->getUserStateFromRequest($this->contentType . '.filter.tag', 'filter_tag', '');
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
		$id .= ':' . $this->state->get('filter.search');
		$id .= ':' . $this->state->get('filter.access');
		$id .= ':' . $this->state->get('filter.published');
		$id .= ':' . $this->state->get('filter.category_id');
		$id .= ':' . $this->state->get('filter.language');

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
		$this->observers->update('onBeforeBuildQuery', array(&$this, &$query));

		// Create a new query object.
		$db = $this->getDb();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->state->get(
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

		// Filter by search in name.
		$search = $this->state->get('filter.search');

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
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		$this->observers->update('onAfterBuildQuery', array(&$this, &$query));

		//echo nl2br(str_replace('#__','jos_',$query));die;
		return $query;
	}
}
