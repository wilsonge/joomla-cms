<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base Cms Model Class
 *
 * @package     Joomla.Libraries
 * @subpackage  Model
 * @since       3.4
 */
abstract class JModelRecord extends JModelData
{
	/**
	 * Method to get a single record.
	 *
	 * @param   integer $pk The id of the primary key.
	 *
	 * @return  mixed  Item in stdClass on success, false on failure
	 *
	 * @since   3.4
	 */
	public function getItem($pk = null)
	{
		if (empty($pk))
		{
			$context = $this->getContext();
			$pk      = (int) $this->getStateVar($context . '.id');
		}

		$activeRecord = $this->getActiveRecord($pk);

		// Convert to a stdClass before adding other data.
		$properties = $activeRecord->getProperties(1);
		$item       = JArrayHelper::toObject($properties);

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}

		return $item;
	}

	/**
	 * Method to increment the hit counter for the record
	 *
	 * @param   integer  $id  Optional ID of the record.
	 *
	 * @return  boolean  True on success
	 *
	 * @since  3.2
	 */
	public function hit($id = null)
	{
		$type = $this->getName();

		if (empty($id))
		{
			$id = $this->getStateVar($type . '.id');
		}

		$item = $this->getTable();

		return $item->hit($id);
	}
}