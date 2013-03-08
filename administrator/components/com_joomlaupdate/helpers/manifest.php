<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Smart download helper. Automatically uses cURL or URL fopen() wrappers to
 * fetch the package.
 * 
 * @package  Joomla.Administrator
 * @since    2.5.10
 */
class ManifestHelper
{
	/**
	 * Downloads from a URL and saves the result as a local file
	 * 
	 * @param   JTableExtension  $extension     The extension loaded
	 * 
	 * @return  array	Array with package folder and manifest filename
	 *
	 * @since   2.5.4
	 */
	public static function getPackageFolder($extension)
	{
		$return = array(
			'package_folder' => null,
			'manifest_filename' => null
		);
		
		// Guess the manifest path and name
		switch($extension->type){
			case 'component':
				// A "component" type extension. We'll have to look for its manifest.
				$return['package_folder'] = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension->element);
				break;
			
			case 'file':
				// A "file" type extension. Its manifest is strictly named and in a predictable path.
				$return['package_folder'] = JPath::clean(JPATH_MANIFESTS . '/files');
				$return['manifest_filename'] = $extension->element . '.xml';
				break;

			case 'language':
				if ($extension->client_id == 0)
				{
					// A site language
					$base_path = JPATH_SITE;
				}
				else
				{
					// An administrator language
					$base_path = JPATH_ADMINISTRATOR;
				}
				$return['package_folder'] = JPath::clean($base_path . '/language/' . $extension->element);
				// A "language" type extension. Its manifest is strictly named and in a predictable path.
				$return['manifest_filename'] = $extension->element . '.xml';
				break;

			case 'library':
				// A "library" type extension. Its manifest is strictly named and in a predictable path.
				$return['package_folder'] = JPATH_MANIFESTS . '/libraries/' . $extension->element;
				$return['manifest_filename'] = $extension->element . '.xml';
				break;

			case 'module':
				// A "module" type extension. We'll have to look for its manifest.
				if ($extension->client_id == 0)
				{
					// A site language
					$base_path = JPATH_SITE;
				}
				else
				{
					// An administrator language
					$base_path = JPATH_ADMINISTRATOR;
				}
				$return['package_folder'] = JPath::clean($base_path . '/modules/' . $extension->element);
				break;

			case 'package':
				// A "package" type extension. Its manifest is strictly named and in a predictable path.
				$return['package_folder'] = JPATH_MANIFESTS . '/packages/' . $extension->element;
				$return['manifest_filename'] = $extension->element . '.xml';
				break;

			case 'plugin':
				// A "plugin" type extension. We'll have to look for its manifest.
				$return['package_folder'] = JPath::clean(JPATH_SITE . '/plugins/' . $extension->folder . '/' . $extension->element);
				break;

			case 'template':
				// A "tempalte" type extension. We'll have to look for its manifest.
				if ($extension->client_id == 0)
				{
					// A site language
					$base_path = JPATH_SITE;
				}
				else
				{
					// An administrator language
					$base_path = JPATH_ADMINISTRATOR;
				}
				$return['package_folder'] = JPath::clean($base_path . '/templates/' . $extension->element);
				break;
		}

		return $return;
	}

	
}
