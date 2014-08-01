<?php
/**
 * @version   0.0.2
 * @package   Babel-U-Lib
 * @copyright Copyright (C) 2011 - 2014 Mathew Lenning. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 * @author    Mathew Lenning - http://babel-university.com/
 */

// No direct access
defined('_JEXEC') or die;

abstract class Babelu_libModelData extends Babelu_libModelCms
{
	/**
	 * Array of JTables
	 * @var array
	 */
	protected $tables = array();

	/**
	 * Method to get the database driver object
	 *
	 * @return  JDatabaseDriver
	 */
	public function getDbo()
	{
		return JFactory::getDbo();
	}

	/**
	 * Method to get the name of the primary key from table
	 *
	 * @param string $tablePrefix
	 * @param string $tableName
	 * @param array  $config
	 *
	 * @return string
	 * @See JTable::getKeyName
	 */
	public function getKeyName( $tablePrefix = null, $tableName = null, $config = array())
	{
		$table = $this->getTable($tablePrefix, $tableName, $config);

		return $table->getKeyName();
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string $prefix The class prefix. Optional.
	 * @param   string $name   The table name. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  ErrorException
	 */
	public function getTable($prefix = null, $name = null, $config = array())
	{
		if (count($config) == 0)
		{
			$config = $this->config;
		}
		else
		{
			//merge sent config to
			//make sure both subject and options
			//are always set.
			//Will not overwrite existing keys
			$config += $this->config;
		}

		if (empty($name))
		{
			$name = ucfirst($config['subject']);
		}

		if (empty($prefix))
		{
			$prefix = ucfirst(substr($config['option'], 4));
		}

		if (!$table = $this->createTable( $prefix, $name, $config))
		{
			throw new ErrorException(JText::_('BABELU_LIB_MODEL_ERROR_TABLE_NAME_NOT_SUPPORTED').': '. $prefix . 'Table' . $name);
		}
		return $table;

	}

	/**
	 * Method to load and return a model object.
	 *
	 * @param   string $prefix The class prefix. Optional.
	 * @param   string $name   The name of the view
	 * @param   array  $config Configuration settings to pass to JTable::getInstance
	 *
	 * @return  mixed   A JTable object or boolean false if failed
	 *
	 * @since   12.2
	 * @see     JTable::getInstance()
	 */
	protected function createTable($prefix, $name, $config = array())
	{
		// Clean the model name
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);
		$name   = preg_replace('/[^A-Z0-9_]/i', '', $name);

		// Make sure we are returning a DBO object
		if (!array_key_exists('dbo', $config))
		{
			$config['dbo'] = $this->getDbo();
		}

		$className = $prefix . 'Table' . $name;

		return new $className($config);
	}

	/**
	 * Method to lock a record for editing
	 *
	 * @param int $pk primary key of record
	 *
	 * @throws InvalidArgumentException
	 * @throws ErrorException
	 * @return boolean
	 * @see JCmsModelData::checkin
	 */
	public function checkout($pk)
	{
		$activeRecord = $this->getActiveRecord($pk);
		$user         = JFactory::getUser();

		$activeRecord->checkout($user->id, $pk);

		return true;
	}

	/**
	 * Method to unlock a record
	 *
	 * @param int $pk primary key
	 *
	 * @throws InvalidArgumentException
	 * @throws ErrorException
	 * @return boolean
	 * @see JCmsModelData::checkout
	 */
	public function checkin($pk)
	{
		// Get an instance of the row to checkout.
		$activeRecord = $this->getActiveRecord($pk);

		$activeRecord->checkin($pk);

		return true;
	}

	/**
	 * Method to get a loaded active record.
	 *
	 * @param int $pk primary key
	 *
	 * @throws ErrorException
	 * @return JTable
	 */
	protected function getActiveRecord($pk)
	{
		// Get an instance of the row to checkout.
		$table = $this->getTable();

		if (!$table->load($pk))
		{
			throw new ErrorException($table->getError());
		}

		return $table;
	}

	/**
	 * Method to check if a table is lockable
	 *
	 * @param JTable $table
	 *
	 * @return boolean
	 */
	protected function isLockable($table)
	{
		$hasCheckedOut     = (property_exists($table, 'checked_out'));
		$hasCheckedOutTime = (property_exists($table, 'checked_out_time'));
		// If there is no checked_out or checked_out_time field, just return true.

		if ($hasCheckedOut && $hasCheckedOutTime)
		{
			return true; // is lockable
		}

		return false; // is not lockable
	}


	/**
	 * Method to check if a record is locked
	 *
	 * @param JTable $activeRecord
	 *
	 * @return boolean
	 */
	protected function isLocked($activeRecord)
	{
		if ($this->isLockable($activeRecord))
		{
			$isCheckedOut = ($activeRecord->checked_out > 0);

			$user            = JFactory::getUser();
			$isCurrentEditor = ($activeRecord->checked_out == $user->get('id'));
			$canOverride     = ($user->authorise('core.admin', 'com_checkin'));

			if ($isCheckedOut && !$isCurrentEditor && !$canOverride)
			{
				return true; // record is locked
			}
		}
		return false; // record is not locked
	}
}
