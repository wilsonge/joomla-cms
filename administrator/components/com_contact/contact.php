<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_contact'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

/**
$controller = JControllerLegacy::getInstance('contact');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
**/

JLoader::registerPrefix('Contact', JPATH_ROOT . '/administrator/components/com_contact');
JLoader::register('ContactHelper', JPATH_ROOT . '/administrator/components/com_contact/helpers/contact.php');
JLoader::register('JHtmlContact', JPATH_ROOT . '/administrator/components/com_contact/helpers/html/contact.php');

JComponentDispatcher::getInstance('com_contact')->dispatch(JFactory::getApplication());