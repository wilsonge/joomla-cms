<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base Cms model.
 *
 * @package     Joomla.Libraries
 * @subpackage  Model
 * @since       3.4
 */
abstract class JModelCms extends JModelDatabase implements JModelCmsInterface
{
	/**
	 * The model (base) name
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $name;

	/**
	 * The injected config
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $config;

	/**
	 * The object content type
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $contentType;

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $option = null;

	/**
	 * The global dispatcher object.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $dispatcher = null;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $text_prefix = null;

	/**
	 * Indicates if the internal state has been set
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $stateSet = false;

	/**
	 * Flag if the internal state should be updated
	 * from request
	 *
	 * @var boolean
	 */
	protected $ignoreRequest;

	/**
	 * The event to trigger on clearing the cache.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $event_clean_cache = 'onContentCleanCache';

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver   $db          The database adpater.
	 * @param   JEventDispatcher  $dispatcher  The event dispatcher
	 * @param   array             $config      An array of configuration options. Must have view
	 *                                         and option keys.
	 *
	 * @since   3.4
	 * @throws  Exception
	 */
	public function __construct(JDatabaseDriver $db = null, JEventDispatcher $dispatcher, $config = array())
	{
		// Set the view name, component name, config and event dispatcher
		$this->name = $config['view'];
		$this->option = $config['option'];
		$this->config = $config;
		$this->dispatcher = $dispatcher ? $dispatcher : JEventDispatcher::getInstance();

		if (array_key_exists('contentType', $config))
		{
			$this->contentType = $config['contentType'];
		}
		else
		{
			$this->contentType = $this->option . '.' . $this->name;
		}

		// If we don't have a db param see if one got set in the config for legacy purposes
		// @deprecated This if block is deprecated and will be removed in Joomla 4.
		if (!$db && array_key_exists('dbo', $config) && $config['dbo'] instanceof JDatabaseDriver)
		{
			$db = $config['dbo'];

			JLog::add('Passing the database object via the config is deprecated. Use the constructor parameter instead', JLog::WARNING, 'deprecated');
		}

		// Register the path for the table object
		$this->addTablePath();

		// Guess the JText message prefix. Defaults to the option.
		if (isset($config['text_prefix']))
		{
			$this->text_prefix = strtoupper($config['text_prefix']);
		}
		elseif (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->option);
		}

		// Used to ignore setting state from the request
		if (!empty($config['ignore_request']))
		{
			$this->ignoreRequest = true;
		}

		// Set the clean cache event
		if (isset($config['event_clean_cache']))
		{
			$this->event_clean_cache = $config['event_clean_cache'];
		}

		// Set the model state. Check we have a JRegistry instance as state was a JObject in
		// legacy MVC
		if (array_key_exists('state', $config) && $config['state'] instanceof JRegistry)
		{
			$state = $config['state'];
		}
		else
		{
			$state = new JRegistry;
		}

		parent::__construct($state, $db);
	}

	/**
	 * Adds to the stack of model table paths in LIFO order.
	 *
	 * @param   mixed  $path  The directory as a string or directories as an array to add.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public static function addTablePath($path = null)
	{
		if($path)
		{
			JTable::addIncludePath($path);

			return;
		}

		// If we haven't been given a path then if there is one set in the config we register that
		// else try constructing a path.
		if (array_key_exists('table_path', $this->config))
		{
			JTable::addIncludePath($config['table_path']);
		}
		else
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/table');
		}
	}

	/**
	 * Method to authorise the current user for an action.
	 * This method is intended to be overridden to allow for customized access rights
	 *
	 * @param   string  $action     ACL action string. e.g. 'core.create'.
	 * @param   string  $assetName  Asset name to check against.
	 * @param   JUser   $user       The user to check the action against
	 *
	 * @return bool
	 * @see JUser::authorise
	 */
	public function allowAction($action, $assetName = null, JUser $user = null)
	{
		// If we have a user instance use it. If we don't have an assetname
		// Use the component name by default
		$assetName = $assetName ? $assetName : $this->option;
		$user = $user ? $user : JFactory::getUser();

		return $user->authorise($action, $assetName);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   3.4
	 */
	protected function canDelete($record)
	{
		// If we can't find a record ID just return false
		if (!empty($record->id))
		{
			// The record is trashed and therefore already deleted!
			if ($record->published != -2)
			{
				return false;
			}

			return $this->allowAction('core.delete', $this->option;

		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state changed. Proxies to allowAction.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   3.4
	 */
	protected function canEditState($record)
	{
		return $this->allowAction('core.edit.state', $this->option;
	}

	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$conf = JFactory::getConfig();

		$options = array(
			'defaultgroup' => ($group) ? $group : $this->option,
			'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$this->dispatcher->trigger($this->event_clean_cache, $options);
	}

	/**
	 * Get the content type for ucm
	 *
	 * @return  string  The content type alias
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * Method to get model state variables
	 *
	 * @return  JRegistry  The state object
	 *
	 * @since   3.4
	 */
	public function getState()
	{
		if (!$this->ignoreRequest && !$this->stateIsSet)
		{
			// Protected method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flag to true.
			$this->stateSet = true;
		}

		return parent::getState();
	}

	/**
	 * Method to get a table object, load it if necessary.
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
	public function getTable($name, $prefix, $options = array())
	{
		if (!$name)
		{
			$name = $this->name;
		}

		if (!$prefix)
		{
			$prefix = $this->option . 'Table';
		}

		// CLean the name and prefix variables up
		$name = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);

		// Make sure we are returning a DBO object
		if (!array_key_exists('dbo', $options))
		{
			$options['dbo'] = $this->getDbo();
		}

		// Try and get table instance
		$table = JTable::getInstance($name, $prefix, $options);

		if ($table instanceof JTableInterface)
		{
			return $table
		}

		// If the table isn't a instance of JTableInterface throw an exception
		throw new RuntimeException(JText::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   3.4
	 */
	abstract protected function populateState()
}
