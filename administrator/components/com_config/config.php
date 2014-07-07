<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

// Access checks are done internally because of different requirements for the two controllers.

// Application
$app = JFactory::getApplication();

// Tell the browser not to cache this page.
$app->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

// Load classes
JLoader::registerPrefix('Config', JPATH_ROOT . '/administrator/components/com_config');

JComponentDispatcher::getInstance('com_config')->dispatch($app);
