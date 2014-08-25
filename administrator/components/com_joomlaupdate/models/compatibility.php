<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! Extension Compatibility Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 * @author      juliopontes <juliopfneto@gmail.com>
 * @since       2.5.10
 */
class JoomlaupdateModelCompatibility extends JModelLegacy
{
	/**
	 * Method to filter 3rd party extensions by the compatibility versions
	 * 
	 * @param   string   $extension       The extension identifier.
	 *
	 * @return  Boolean  True if extension is loaded other else false
	 * @since   2.5.10
	 */
	public function extensionExists($extension)
	{
		$value = JTable::getInstance('extension');
		$value->load( $value->find(array('element' => $extension)) );
		return $value->extension_id != 0 ? true : false ;
	}

	/**
	 * Method to filter 3rd party extensions by the compatibility versions
	 * 
	 * @param   array   $extension       The extension identifier.
	 *
	 * @return  array  An array of data items.
	 * @since   2.5.10
	 */
	public function checkCompatibility()
	{
		$input = JFactory::getApplication()->input;
		$extension = $input->getCmd('extension');
		$joomla_version = $input->getCmd('jversion',JVERSION);

		$value = JTable::getInstance('extension');
		$value->load( $value->find(array('element' => $extension)) );

		//return variable
		$items_compatible = array(
		);

		//import installer class for finding manifest xml file
		jimport('joomla.installer.installer');
		$installer 	= JInstaller::getInstance();

		$findManifes = ManifestHelper::getPackageFolder($value);
		$package_folder = $findManifes['package_folder'];
		$manifest_filename = $findManifes['manifest_filename'];

		// Set up the installer's source path
		$installer->setPath('source', $package_folder);

		// Load the extension's manifest
		$manifest = null;
		if (!is_null($manifest_filename)) {
			// We already have a manifest path. Let's try to load it.
			$manifest = $installer->isManifest($package_folder . '/' . $manifest_filename);
		} else {
			// We don't have a manifest path. Let's try to find one.
			if ($installer->findManifest() !== false) {
				$manifest = $installer->getManifest();
			}
		}

		if (!is_object($manifest)) {
			// This extension's manifest is missing or corrupt
			$items_compatible['na'][] = $value->element;
			continue;
		}

		// Check inside manifest xml for compatibility tags
		$compatiblity = new JCompatibility($manifest->compatibilities);
		$element = $manifest->compatibilities;

		if ($element) {
			foreach ($element->children() as $compatible) {
				$compatible_found = false;
				$with = $compatible['with'];
				switch ($with)
				{
					case 'joomla':
						$current_version = $joomla_version;
						$compatible_found = $compatiblity->check($current_version, $with);
						break;
					case 'php':
						$current_version = PHP_VERSION;
						$compatible_found = $compatiblity->check($current_version, $with);
						break;
					case 'mysql':
						$db = JFactory::getDbo();
						$connectors = $db->getConnectors();

						foreach ($connectors as $connector)
						{
							if ($connector === $with)
							{
								$current_version = $db->getVersion();
								$compatible_found = $compatiblity->check($current_version, $with);
								break;
							}
							else
							{
								// Connector doesn't exist therefore mark as true
								$compatible_found = true;
							}
						}
						break;
					case 'postgresql':
						$db = JFactory::getDbo();
						$connectors = $db->getConnectors();

						foreach ($connectors as $connector)
						{
							if ($connector === $with)
							{
								$current_version = $db->getVersion();
								$compatible_found = $compatiblity->check($current_version, $with);
								break;
							}
							else
							{
								// Connector doesn't exist therefore mark as true
								$compatible_found = true;
							}
						}
						break;
					case 'sqlazure':
						$db = JFactory::getDbo();
						$connectors = $db->getConnectors();

						foreach ($connectors as $connector)
						{
							if ($connector === $with)
							{
								$current_version = $db->getVersion();
								$compatible_found = $compatiblity->check($current_version, $with);
								break;
							}
							else
							{
								// Connector doesn't exist therefore mark as true
								$compatible_found = true;
							}
						}
						break;
					default:
						$extensionInfo = JTable::getInstance('extension');
						$extensionInfo->load( $extensionInfo->find(array('element' => (string)$with)) );
						$extensionParams = new JRegistry($extensionInfo->manifest_cache);
						$current_version = $extensionParams->get('version');
						$compatible_found = $compatiblity->check($current_version, $with);
						break;
				}
				
				if (!$compatible_found) {
					$item = new stdclass;
					$item->name = (string)$with;
					$item->version = $current_version;
					$item->min_compatible_version = (string)$compatible->include->versions['from'];
					$item->max_compatible_version = (string)$compatible->include->versions['to'];
					$items_compatible[] = $item;
				}
			}
		}

		return $items_compatible;
	}	
}
