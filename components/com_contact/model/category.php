<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 * @since       1.5
 */
class ContactModelCategory extends JModelAdministrator
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $_item = null;

	protected $_articles = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;

	/**
	 * The category that applies.
	 *
	 * @access    protected
	 * @var        object
	 */
	protected $_category = null;

	/**
	 * The list of other newfeed categories.
	 *
	 * @access    protected
	 * @var        array
	 */
	protected $_categories = null;

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
				array(
					'name' => 'id',
					'dataKeyName' => 'a.id',
				),
				array(
					'name' => 'name',
					'dataKeyName' => 'a.name',
				),
				array(
					'name' => 'con_position',
					'dataKeyName' => 'a.con_position',
				),
				array(
					'name' => 'suburb',
					'dataKeyName' => 'a.suburb',
				),
				array(
					'name' => 'state',
					'dataKeyName' => 'a.state',
				),
				array(
					'name' => 'country',
					'dataKeyName' => 'a.country',
				),
				array(
					'name' => 'ordering',
					'dataKeyName' => 'a.ordering',
				),
				array(
					'name' => 'sortname1',
					'dataKeyName' => 'a.sortname1',
				),
				array(
					'name' => 'sortname2',
					'dataKeyName' => 'a.sortname2',
				),
				array(
					'name' => 'sortname3',
					'dataKeyName' => 'a.sortname3',
				),

				// @todo deal with sortname
				//'sortname',
			);
		}

		parent::__construct(null, null, null, $config);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		// Convert the params field into an object, saving original in _params
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = & $items[$i];
			if (!isset($this->_params))
			{
				$params = new JRegistry;
				$params->loadString($item->params);
				$item->params = $params;
			}
			$this->tags = new JHelperTags;
			$this->tags->getItemTags('com_contact.contact', $item->id);

		}

		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string    An SQL query
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db = $this->getDb();
		$query = $db->getQuery(true);

		// Select required fields from the categories.
		//sqlsrv changes
		$case_when = ' CASE WHEN ';
		$case_when .= $query->charLength('a.alias', '!=', '0');
		$case_when .= ' THEN ';
		$a_id = $query->castAsChar('a.id');
		$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= $a_id . ' END as slug';

		$case_when1 = ' CASE WHEN ';
		$case_when1 .= $query->charLength('c.alias', '!=', '0');
		$case_when1 .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when1 .= ' ELSE ';
		$case_when1 .= $c_id . ' END as catslug';
		$query->select($this->getStateVar('list.select', 'a.*') . ',' . $case_when . ',' . $case_when1)
		// TODO: we actually should be doing it but it's wrong this way
		//	. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
		//	. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug ');
			->from($db->quoteName('#__contact_details') . ' AS a')
			->join('LEFT', '#__categories AS c ON c.id = a.catid')
			->where('a.access IN (' . $groups . ')');

		// Filter by category.
		if ($categoryId = $this->getStateVar('category.id'))
		{
			$query->where('a.catid = ' . (int) $categoryId)
				->where('c.access IN (' . $groups . ')');
		}

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author")
			->select("ua.email AS author_email")

			->join('LEFT', '#__users AS ua ON ua.id = a.created_by')
			->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

		// Filter by state
		$state = $this->getStateVar('filter.published');

		if (is_numeric($state))
		{
			$query->where('a.published = ' . (int) $state);
		}
		// Filter by start and end dates.
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSql());

		if ($this->getStateVar('filter.publish_date'))
		{
			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
				->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}

		// Filter by search in title
		$search = $this->getStateVar('list.filter');
		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(a.name LIKE ' . $search . ')');
		}

		// Filter by language
		if ($this->getStateVar('filter.language'))
		{
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		// Set sortname ordering if selected
		if ($this->getStateVar('list.ordering') == 'sortname')
		{
			$query->order($db->escape('a.sortname1') . ' ' . $db->escape($this->getStateVar('list.direction', 'ASC')))
				->order($db->escape('a.sortname2') . ' ' . $db->escape($this->getStateVar('list.direction', 'ASC')))
				->order($db->escape('a.sortname3') . ' ' . $db->escape($this->getStateVar('list.direction', 'ASC')));
		}
		else
		{
			$query->order($db->escape($this->getStateVar('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getStateVar('list.direction', 'ASC')));
		}

		return $query;
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
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_contact');

		// List state information
		$format = $app->input->getWord('format');
		if ($format == 'feed')
		{
			$limit = $app->get('feed_limit');
		}
		else
		{
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
		}
		$this->state->set('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->state->set('list.start', $limitstart);

		// Optional filter text
		$this->state->set('list.filter', $app->input->getString('filter-search'));

		// Get list ordering default from the parameters
		$menuParams = new JRegistry;
		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}
		$mergedParams = clone $params;
		$mergedParams->merge($menuParams);

		$orderCol = $app->input->get('filter_order', $mergedParams->get('initial_sort', 'ordering'));
		if (!in_array($orderCol, $this->filterFields))
		{
			$orderCol = 'ordering';
		}
		$this->state->set('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}
		$this->state->set('list.direction', $listOrder);

		$id = $app->input->get('id', 0, 'int');
		$this->state->set('category.id', $id);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_contact')) && (!$user->authorise('core.edit', 'com_contact')))
		{
			// limit to published for people who can't edit or edit.state.
			$this->state->set('filter.published', 1);

			// Filter by start and end dates.
			$this->state->set('filter.publish_date', true);
		}
		$this->state->set('filter.language', JLanguageMultilang::isEnabled());

		// Load the parameters.
		$this->state->set('params', $params);
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param   integer  An optional ID
	 *
	 * @return  object
	 * @since   1.5
	 */
	public function getCategory()
	{
		if (!is_object($this->_item))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;

			if ($active)
			{
				$params->loadString($active->params);
			}

			$options = array();
			$options['countItems'] = $params->get('show_cat_items', 1) || $params->get('show_empty_categories', 0);
			$categories = JCategories::getInstance('Contact', $options);
			$this->_item = $categories->get($this->getStateVar('category.id', 'root'));
			if (is_object($this->_item))
			{
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;
				if ($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}
				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else
			{
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	/**
	 * Get the parent category.
	 *
	 * @param   integer  An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 */
	public function getParent()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_parent;
	}

	/**
	 * Get the sibling (adjacent) categories.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 */
	function &getLeftSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_leftsibling;
	}

	function &getRightSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 *
	 * @param   integer  An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 */
	function &getChildren()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_children;
	}

	/**
	 * Increment the hit counter for the category.
	 *
	 * @param   integer  $pk  Optional primary key of the category to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 *
	 * @since   3.2
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getStateVar('category.id');

			$table = JTable::getInstance('Category', 'JTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}
}
