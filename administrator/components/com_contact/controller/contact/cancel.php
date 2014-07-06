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
class ContactControllerContactCancel extends JControllerCancel
{
	public function execute()
	{
		parent::execute();

		$this->setRedirect(JRoute::_('index.php?option=com_contact&view=contacts', false));
	}
}
