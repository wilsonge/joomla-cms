<?php
/**
 * @package    Joomla.Libraries
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class JNamespaceMap
 *
 * @since  __DEPLOY_VERSION__
 */
class JNamespacePsr4Map
{
	/**
	 * Path to the autoloader
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $file = '';

	/**
	 * Constructor. For PHP 5.5 compatibility we must set the file property like this
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		$this->file = JPATH_LIBRARIES . '/autoload_psr4.php';
	}

	/**
	 * Check if the file exists
	 *
	 * @return  bool
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function exists()
	{
		if (!file_exists($this->file))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if the namespace mapping file exists, if not create it
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function ensureMapFileExists()
	{
		// Ensure that the database is connected (because it isn't in the installer where this function gets called from
		// CMSApplication
		if (!$this->exists() && JFactory::getDbo()->connected())
		{
			$this->create();
		}
	}

	/**
	 * Create the namespace file
	 *
	 * @return  bool
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function create()
	{
		$extensions = $this->getNamespacedExtensions();

		$elements = array();

		foreach ($extensions as $extension)
		{
			$element       = $extension->element;
			$extensionType = $extension->type;
			$clientId      = $extension->client_id;
			$baseNamespace = str_replace("\\", "\\\\", $extension->namespace);

			if ($extensionType === 'component')
			{
				if (file_exists(JPATH_ADMINISTRATOR . '/components/' . $element))
				{
					// TODO: Somehow we need to remove the /administrator/ section here in favour of whatever is in
					//       /includes.defines.php
					$elements[$baseNamespace . '\\\\Administrator'] = array('/administrator/components/' . $element);
				}

				if (file_exists(JPATH_ROOT . '/components/' . $element))
				{
					$elements[$baseNamespace . '\\\\Site'] = array('/components/' . $element);
				}
			}
			elseif ($extensionType === 'module' && $clientId === '0')
			{
				if (file_exists(JPATH_ROOT . '/modules/' . $element))
				{
					$elements[$baseNamespace . '\\\\Site'] = array('/modules/' . $element);
				}
			}
			elseif (strtolower($extensionType) === 'module' && $clientId === '1')
			{
				if (file_exists(JPATH_ROOT . '/administrator/modules/' . $element))
				{
					$elements[$baseNamespace . '\\\\Administrator'] = array('/administrator/modules/' . $element);
				}
			}
		}

		$this->writeNamespaceFile($elements);

		return true;
	}

	/**
	 * Write the Namespace mapping file
	 *
	 * @param   array  $elements  Array of elements
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function writeNamespaceFile($elements)
	{
		$content   = array();
		$content[] = "<?php";
		$content[] = 'return array(';

		foreach ($elements as $namespace => $paths)
		{
			$pathString = '';

			foreach ($paths as $path)
			{
				$pathString .= '"' . $path . '",';
			}

			$content[] = "\t'" . $namespace . "'" . ' => array(JPATH_ROOT . ' . $pathString . '),';
		}

		$content[] = ');';

		file_put_contents($this->file, implode("\n", $content));
	}

	/**
	 * Get all namespaced extensions from the database
	 *
	 * @return  mixed|false
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getNamespacedExtensions()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('extension_id', 'element', 'namespace', 'type', 'client_id')))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('namespace') . ' IS NOT NULL AND ' . $db->quoteName('namespace') . ' != ""');

		$db->setQuery($query);

		$extensions = $db->loadObjectList();

		return $extensions;
	}
}
