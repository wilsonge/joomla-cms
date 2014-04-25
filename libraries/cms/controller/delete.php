<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

class JCmsControllerDelete extends JCmsControllerBase
{
	/**
	 * (non-PHPdoc)
	 * @see JController::execute()
	 */
	public function execute()
	{
		//Check for request forgeries
		$this->validateSession();

		$config = $this->config;
		$url = 'index.php?option='.$config['option'].'&task=display.'.$config['subject'];
		$prefix = $this->getPrefix();
		$model = $this->getModel($prefix, $config['subject'], $config);

		If(!$model->allowAction('core.delete'))
		{
			$msg = $this->translate('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED');
			$this->setRedirect($url, $msg, 'error');
			return false;
		}

		$input = $this->input;

		$cid = $input->post->get('cid', array(), 'array');
		$totalCids = count($cid);

		if (!is_array($cid) || $totalCids < 1)
		{
			$msg = $this->translate('JLIB_APPLICATION_ERROR_NO_ITEM_SELECTED');
			$this->setRedirect($url, $msg, 'error');
			return false;
		}
			

		// Make sure the item ids are integers
		$cid = $this->cleanCid($cid);

		try
		{
			$model->delete($cid);
		}
		catch (Exception $e)
		{
			$msg = $e->getMessage();
			$this->setRedirect($url, $msg, 'warning');
			return false;
		}

		$msg = $this->translate('JLIB_APPLICATION_MSG_ITEMS_DELETED');
		$this->setRedirect($url, $msg, 'message');
		return true;
	}
}