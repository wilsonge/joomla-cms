<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/route.php';

JLoader::registerPrefix('Contact', JPATH_ROOT . '/components/com_contact');

JComponentDispatcher::getInstance('com_contact')->dispatch();
/**
$controller = JControllerLegacy::getInstance('Contact');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
**/