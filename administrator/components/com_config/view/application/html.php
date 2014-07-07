<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View for the global configuration
 *
 * @package     Joomla.Administrator
 * @subpackage  com_config
 * @since       3.2
 */
class ConfigViewApplicationHtml extends JViewHtmlLegacy
{
	protected $components;

	protected $form;

	protected $data;

	protected $userIsSuperAdmin;

	protected $ftp;

	/**
	 * Method to display the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.2
	 */
	public function render()
	{
		// @todo don't think these params are being used
		/**
		// Get the params for com_users.
		$usersParams = JComponentHelper::getParams('com_users');

		// Get the params for com_media.
		$mediaParams = JComponentHelper::getParams('com_media');

		$this->usersParams = &$usersParams;
		$this->mediaParams = &$mediaParams;
		**/

		// Set the components that have a config. This is also used in the getData() function
		$this->components = ConfigHelperConfig::getComponentsWithConfig();
		ConfigHelperConfig::loadLanguageForComponents($this->components);

		// Set the data variables
		$data = $this->getData();

		$this->form = $data['form'];
		$this->data = $data['data'];
		$this->userIsSuperAdmin = $data['userIsSuperAdmin'];
		$this->ftp = $data['ftp'];	

		$this->addToolbar();

		return parent::render();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since	3.2
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_CONFIG_GLOBAL_CONFIGURATION'), 'equalizer config');
		JToolbarHelper::apply('config.save.application.apply');
		JToolbarHelper::save('config.save.application.save');
		JToolbarHelper::divider();
		JToolbarHelper::cancel('config.cancel.application');
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_SITE_GLOBAL_CONFIGURATION');
	}

	/**
	 * Sets the data to be given to the renderer
	 *
	 * @return  array
	 *
	 * @since   3.4
	 */
	public function getData()
	{
		$model = $this->getModel();

		try
		{
			// Load Form and Data
			$user = JFactory::getUser();
			$form = $model->getForm();
			$data = $model->getData();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// Load settings for the FTP layer.
		$ftp = JClientHelper::setCredentialsFromRequest('ftp');

		if (!isset($this->components))
		{
			$this->components = ConfigHelperConfig::getComponentsWithConfig();
		}

		$data = array(
			'form' => $form,
			'data' => $data,
			'userIsSuperAdmin' => $user->authorise('core.admin'),
			'ftp' => $ftp,
			'components' => $this->components,
		);

		return $data;
	}
}
