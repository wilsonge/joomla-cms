<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       2.5.4
 */

defined('_JEXEC') or die;

/**
 * Joomla! Update's Default View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 * @since       2.5.10
 */
class JoomlaupdateViewCompatibility extends JViewLegacy 
{
	/**
	 * Renders the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @since  2.5.10
	 */
	public function display($tpl=null)
	{
		$input = JFactory::getApplication()->input;
		
		$input->set('hidemainmenu', '1');
		$input->set('tmpl', 'component');
		
		$model = $this->getModel();
		$items = $model->checkCompatibility();
		
		$this->assignRef('items', $items);
		
		parent::display($tpl);
	}
}