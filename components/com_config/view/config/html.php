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
 * View for the global configuration
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
 */
class ConfigViewConfigHtml extends JViewHtmlLegacy
{
	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.2
	 */
	public function render()
	{
		$user = JFactory::getUser();
		$this->userIsSuperAdmin = $user->authorise('core.admin');

		$data = $this->getData();
		$this->form = $data['form'];

		return parent::render();
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
		$data = array(
			'form' => $model->getForm(),
		);

		return $data;
	}
}
