<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Base Display Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_config
 * @since       3.2
 * @note        Needed for front end view
 */
class ConfigControllerApplicationDisplay extends JControllerDisplay
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
		//$renderer = new JRendererLegacy(null, $this->app);
		$renderer = new JRendererLegacy($this->config);

		return $renderer;
	}
}
