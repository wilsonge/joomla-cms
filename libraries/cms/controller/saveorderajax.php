<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base Display Controller
 *
 * @package     Joomla.Libraries
 * @subpackage  Controller
 * @since       3.4
*/

class JControllerSaveorderajax extends JControllerCms
{
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		// Check for request forgeries
		$this->factory->checkSession();

		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		try
		{
			$model = $this->getModel();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
		}

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			// @todo Convert this to use JResponseJson
			echo "1";
		}

		// Close the application
		$this->app->close();
	}
}
