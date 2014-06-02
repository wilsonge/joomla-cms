<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Plugin;

use Joomla\Registry\Registry;
use \JFactory;

defined('JPATH_PLATFORM') or die;

/**
 * Plugin Class for new style plugins
 *
 * @package     Joomla.Platform
 * @subpackage  Plugin
 * @since       3.4
 */
abstract class Plugin
{
	/**
	 * A JRegistry object holding the parameters for the plugin
	 *
	 * @var    Registry
	 * @since  3.4
	 */
	public $params = null;

	/**
	 * The name of the plugin
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $pluginName = null;

	/**
	 * The plugin type
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $type = null;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $autoloadLanguage = false;

	/**
	 * Constructor
	 *
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'type', 'params'
	 *
	 * @since   3.4
	 */
	public function __construct($config = array())
	{
		// Get the parameters.
		if (isset($config['params']))
		{
			if ($config['params'] instanceof Registry)
			{
				$this->params = $config['params'];
			}
			else
			{
				$this->params = new Registry;
				$this->params->loadString($config['params']);
			}
		}

		// Get the plugin name.
		if (isset($config['name']))
		{
			$this->pluginName = $config['name'];
		}

		// Get the plugin type.
		if (isset($config['type']))
		{
			$this->type = $config['type'];
		}

		// Load the language files if needed.
		if ($this->autoloadLanguage)
		{
			$this->loadLanguage();
		}

		if ($this->app)
		{
			$this->app = JFactory::getApplication();
		}

		if ($this->db)
		{
			$this->db = JFactory::getDbo();
		}
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string  $extension  The extension for which a language file should be loaded
	 * @param   string  $basePath   The basepath to use
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 *
	 * @since   3.4
	 */
	protected function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->type . '_' . $this->pluginName;
		}

		$lang = JFactory::getLanguage();

		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->type . '/' . $this->pluginName, null, false, true);
	}
}
