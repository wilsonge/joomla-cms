<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       3.4
 */
class ContactControllerDisplay extends JControllerDisplay
{
	/*
	 * Allows the renderer class to be injected into the model to be set
	 *
	 * @return  RendererInterface  The renderer object
	 *
	 * @since   3.4
	 */
	protected function getRenderer()
	{
		// Set the renderer
		JLoader::register('ContactRendererContact', JPATH_ROOT . '/administrator/components/com_contact/renderer/contact.php');
		$renderer = new ContactRendererContact($this->config);

		return $renderer;
	}
}
