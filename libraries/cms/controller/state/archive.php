<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

class JControllerStateArchive extends JControllerStateBase
{
	public function execute()
	{
		try
		{
			parent::execute();
		}
		catch (Exception $e)
		{
			$msg = $e->getMessage();
			$this->abort($msg, 'error');

			return false;
		}

		$config = $this->config;
		$url    = 'index.php?option=' . $config['option'] . '&task=display.' . $config['subject'];
		$msg = $this->translate('JLIB_APPLICATION_MSG_ITEMS_ARCHIVED');
		$this->setRedirect($url, $msg, 'message');

		return true;
	}


	/**
	 * @see JControllerStateBase::execute()
	 */
	protected function updateRecordState($model, $cid)
	{
		$model->updateRecordState($cid, 'archive');
	}
}