<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Prototype model for an item or collection of items.
 *
 * @package     Joomla.Libraries
 * @subpackage  Model
 * @since       3.4
 */
class JModelObserverFilterTags extends JModelObserver
{
	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 * Creates the associated tags helper class instance
	 * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JObservableInterface  $observableObject  The subject object to be observed
	 * @param   array                 $params            ( 'typeAlias' => $typeAlias )
	 *
	 * @return  JTableObserverTags
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);

		return $observer;
	}

	/**
	 * This event runs after we have built the query used to fetch a record
	 * list in a model. It is used to apply automatic query filters.
	 *
	 * @param   JModelCms       &$model  The model which calls this event
	 * @param   JDatabaseQuery  &$query  The model which calls this event
	 *
	 * @return  void
	 */
	public function onAfterBuildQuery(&$model, &$query)
	{
		$db = $model->getDb();
		$state = $model->getState();

		// Filter by a single tag.
		$tagId = $state->get('filter.tag');

		if (is_numeric($tagId))
		{
			$key = $model->getTable()->getKeyName();
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.' . $key)
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote($model->getContentType())
				);
		}
	}
}
