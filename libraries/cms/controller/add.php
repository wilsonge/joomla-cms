<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

class JControllerAdd extends JControllerDisplay
{
	/**
	 * Instantiate the controller.
	 *
	 * @param   JInput            $input  The input object.
	 * @param   JApplicationBase  $app    The application object.
	 * @param   array             $config Configuration
	 * @since  12.1
	 */
	public function __construct(JInput $input, $app = null, $config = array())
	{
		$input->set('layout', 'edit');

		parent::__construct($input, $app, $config);
	}

	/**
	 * (non-PHPdoc)
	 * @see JControllerDisplay::execute()
	 */
	public function execute()
	{
		$config = $this->config;
		$prefix = $this->getPrefix();
		$model = $this->getModel($prefix, $config['subject'], $config);

		if (!$model->allowAction('core.create'))
		{
			$msg = $this->translate('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED');
			$url = 'index.php?option='.$config['option'].'&task=display.'.$config['subject'];
			$this->setRedirect($url, $msg, 'error');
			return false;
		}

		return parent::execute();	
	}

}