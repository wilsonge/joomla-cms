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
 * Display Controller for global configuration
 *
 * @package     Joomla.Site
 * @subpackage  com_config
 * @since       3.2
*/
class ConfigControllerConfigDisplay extends JControllerDisplay
{
	/*
	 * Permission needed for the action. Defaults to most restrictive
	 *
	 * @var  string
	 * @since  3.4
	 */
	public $permission = 'core.admin';

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
		$renderer = new JRendererLegacy(null, $this->app);

		return $renderer;
	}
}
