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
class ContactControllerContactDisplay extends JControllerDisplayform
{
	public function __construct(JInput $input = null, JApplicationCms $app = null, array $config = array(), JDocument $doc = null)
	{
		parent::__construct($input, $app, $config, $doc);

		$this->input->set('view', 'contact');
	}

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
		$renderer = new ContactRendererContact($this->config);

		return $renderer;
	}
}
