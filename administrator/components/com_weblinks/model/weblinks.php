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
 * Methods supporting a list of weblink records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 * @since       1.6
 */
class WeblinksModelWeblinks extends JModelAdministrator
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'][] = array('name' => 'id', 'dataKeyName' => 'a.id', 'sortable' => true, 'searchable' => true);
			$config['filter_fields'][] = array('name' => 'title', 'dataKeyName' => 'a.title', 'sortable' => true, 'searchable' => true);
			$config['filter_fields'][] = array('name' => 'alias', 'dataKeyName' => 'a.alias', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'checked_out', 'dataKeyName' => 'a.checked_out', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'checked_out_time', 'dataKeyName' => 'a.checked_out_time', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'category_id', 'dataKeyName' => 'a.catid', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'category_title', 'dataKeyName' => 'c.title', 'sortable' => false, 'searchable' => true);
			$config['filter_fields'][] = array('name' => 'state', 'dataKeyName' => 'a.state', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'access', 'dataKeyName' => 'a.access', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'access_level', 'dataKeyName' => 'ag.title', 'sortable' => true, 'searchable' => true);
			$config['filter_fields'][] = array('name' => 'created', 'dataKeyName' => 'a.created', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'created_by', 'dataKeyName' => 'a.created_by', 'sortable' => true, 'searchable' => true);
			$config['filter_fields'][] = array('name' => 'ordering', 'dataKeyName' => 'a.ordering', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'featured', 'dataKeyName' => 'a.featured', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'language', 'dataKeyName' => 'a.language', 'sortable' => true, 'searchable' => true);
			$config['filter_fields'][] = array('name' => 'hits', 'dataKeyName' => 'a.hits', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'publish_up', 'dataKeyName' => 'a.publish_up', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'publish_down', 'dataKeyName' => 'a.publish_down', 'sortable' => true, 'searchable' => false);
			$config['filter_fields'][] = array('name' => 'url', 'dataKeyName' => 'a.url', 'sortable' => true, 'searchable' => false);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->getContext() . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->getContext() . '.filter.access', 'filter_access', null, 'string');
		$this->setState('filter.access', $accessId);

		$published = $this->getUserStateFromRequest($this->getContext() . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$categoryId = $this->getUserStateFromRequest($this->getContext() . '.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->getContext() . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$tag = $this->getUserStateFromRequest($this->getContext() . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_weblinks');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery(JDatabaseQuery $query = null)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid,' .
				'a.hits,' .
				'a.state, a.access, a.ordering,' .
				'a.language, a.publish_up, a.publish_down'
			)
		);
		$query->from($db->quoteName('#__weblinks') . ' AS a');

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


		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}


		$tagId = $this->getState('filter.tag');
		// Filter by a single tag.
		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_weblinks.weblink')
				);
		}

		// add the default filters
		$query = parent::getListQuery($query);

		return $query;
	}

	/**
	 * @see JModelCms::allowAction()
	 */
	public function allowAction($action, $assetName = null, $activeRecord = null)
	{
		if (is_object($activeRecord) && !empty($activeRecord->catid))
		{
			$config = $this->config;
			$assetName = $config['option'].'.category.'.(int) $activeRecord->catid;
		}

		return parent::allowAction($action, $assetName, $activeRecord);
	}
}
