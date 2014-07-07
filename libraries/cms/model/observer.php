<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base Cms observer.
 *
 * @package     Joomla.Libraries
 * @subpackage  Model
 * @since       3.4
 */
abstract class JModelObserver implements JObserverInterface
{
	/**
	 * The observed model
	 *
	 * @var    JModelCms
	 * @since  3.4
	 */
	protected $model;

	/**
	 * Constructor: Associates to $model $this observer
	 *
	 * @param   JTableInterface  $table  Table to be observed
	 *
	 * @since   3.1.2
	 */
	public function __construct(JModelCms $model)
	{
		$model->attachObserver($this);
		$this->model = $model;
	}

	/**
	 * This event runs before saving data in the model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 * @param   array      &$data   The data to save
	 *
	 * @return  void
	 */
	public function onBeforeSave(&$model, &$data)
	{
	}

	/**
	 * This event runs before deleting a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onBeforeDelete(&$model)
	{
	}

	/**
	 * This event runs before publishing a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onBeforePublish(&$model)
	{
	}

	/**
	 * This event runs before registering a hit on a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onBeforeHit(&$model)
	{
	}

	/**
	 * This event runs when we are building the query used to fetch a record
	 * list in a model
	 *
	 * @param   JModelCms       &$model  The model which calls this event
	 * @param   JDatabaseQuery  &$query  The query being built
	 *
	 * @return  void
	 */
	public function onBeforeBuildQuery(&$model, &$query)
	{
	}

	/**
	 * This event runs after saving a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onAfterSave(&$model)
	{
	}

	/**
	 * This event runs after deleting a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onAfterDelete(&$model)
	{
	}

	/**
	 * This event runs after publishing a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onAfterPublish(&$model)
	{
	}

	/**
	 * This event runs after registering a hit on a record in a model
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onAfterHit(&$model)
	{
	}

	/**
	 * This event runs after we have built the query used to fetch a record
	 * list in a model
	 *
	 * @param   JModelCms       &$model  The model which calls this event
	 * @param   JDatabaseQuery  &$query  The query being built
	 *
	 * @return  void
	 */
	public function onAfterBuildQuery(&$model, &$query)
	{
	}

	/**
	 * This event runs after getting a single item
	 *
	 * @param   JModelCms  &$model  The model which calls this event
	 * @param   FOFTable   &$record  The record loaded by this model
	 *
	 * @return  void
	 */
	public function onAfterGetItem(&$model, &$record)
	{
	}
}
