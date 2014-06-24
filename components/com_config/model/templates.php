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
 * Template style model.
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
 */
class ConfigModelTemplates extends JModelCmsform
{
	public function getServiceData(JInput $input = null)
	{
		// Set up variables needed
		$app =  JFactory::getApplication();
		$input = $input ? $input : $app->input;
		$document = JFactory::getDocument();
		$viewName = $input->getWord('view', 'config');

		// Access back-end com_config
		JLoader::register('TemplatesController', JPATH_ADMINISTRATOR . '/components/com_templates/controller.php');
		JLoader::register('TemplatesViewStyle', JPATH_ADMINISTRATOR . '/components/com_templates/views/style/view.json.php');
		JLoader::register('TemplatesModelStyle', JPATH_ADMINISTRATOR . '/components/com_templates/models/style.php');

		$displayClass = new TemplatesController;

		// Set back-end required params
		$document->setType('json');
		$input->set('id', $app->getTemplate('template')->id);

		// Execute back-end controller
		$serviceData = json_decode($displayClass->display(), true);

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
	 * Note. Calling getState in this method will result in recursion.
	 * 
	 * @return  null
	 *
	 * @since   3.2
	 */
	protected function populateState()
	{
		$state = $this->loadState();

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_templates');
		$state->set('params', $params);

		$this->setState($state);
	}

	/**
	 * Method to load the form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm	A JForm object on success, false on failure
	 *
	 * @since   3.2
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = sha1($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->forms[$hash]) && !$clear)
		{
			return $this->forms[$hash];
		}

		try
		{
			$form = new JForm('com_config.templates');
			$data = array();

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		}
		catch (Exception $e)
		{
			// Throw any exceptions upstream
			throw new RuntimeException($e->getMessage());
		}

		// Store the form for later.
		$this->forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to preprocess the form
	 *
	 * @param   JForm   $form   A form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  Plugin group to load
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @throws	Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		$lang = JFactory::getLanguage();

		$template = JFactory::getApplication()->getTemplate();

		jimport('joomla.filesystem.path');

		// Load the core and/or local language file(s).
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
		||	$lang->load('tpl_' . $template, JPATH_BASE . '/templates/' . $template, null, false, true);

		// Look for com_config.xml, which contains fileds to display
		$formFile	= JPath::clean(JPATH_BASE . '/templates/' . $template . '/com_config.xml');

		if (!file_exists($formFile))
		{
			// If com_config.xml not found, fall back to templateDetails.xml
			$formFile	= JPath::clean(JPATH_BASE . '/templates/' . $template . '/templateDetails.xml');
		}

		if (file_exists($formFile))
		{
			// Get the template form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile))
		{
			throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}
}
