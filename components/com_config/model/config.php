<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Model for the global configuration
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
 */
class ConfigModelConfig extends JModelCmsform
{
	public function getServiceData(JInput $input = null)
	{
		$input = $input ? $input : JFactory::getApplication()->input;
		$document = JFactory::getDocument();
		$viewName = $input->getWord('view', 'config');

		// Access back-end com_config
		JLoader::registerPrefix(ucfirst($viewName), JPATH_ADMINISTRATOR . '/components/com_config');
		$displayClass = new ConfigControllerApplicationDisplay;

		// Set back-end required params
		$document->setType('json');
		$input->set('view', 'application');

		// Execute back-end controller
		$serviceData = json_decode($displayClass->execute(), true);

		// Reset params back after requesting from service
		$document->setType('html');
		$input->set('view', $viewName);

		return $serviceData;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   3.2
	 */
	protected function loadFormData()
	{
		return $this->getServiceData();
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * In com_config we just return an empty JRegistry object as there is no JTable
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   3.2
	 */
	protected function populateState()
	{
		return new JRegistry;
	}
}
