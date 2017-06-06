<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Application;

defined('JPATH_PLATFORM') or die;

use JLoader;

/**
 * Trait for application classes which ensures the namespace mapper exists and includes it.
 *
 * @since  4.0
 */
trait ExtensionNamespaceMapper
{
	/**
	 * Allows the application to load a custom or default identity.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function createExtensionNamespaceMap()
	{
		JLoader::register('JNamespacePsr4Map', JPATH_LIBRARIES . '/namespacemap.php');
		$extensionPsr4Loader = new \JNamespacePsr4Map;
		$extensionPsr4Loader->ensureMapFileExists();
	}

	/**
	 * Loads the psr4 namespace file and registers all extensions namespaces.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function registerExtensions()
	{
		$map = require JPATH_LIBRARIES . '/autoload_psr4.php';

		foreach ($map as $namespace => $paths) {
			foreach ($paths as $key => $path)
			{
				$reset = false;

				if ($key === 0)
				{
					$reset = true;
				}

				JLoader::registerNamespace($namespace, $path, $reset, false, 'psr4');
			}
		}
	}
}
