<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\ArticlesLatest\Site\Helper\ArticlesLatestHelper;
use Joomla\CMS\MVC\Factory\MVCFactoryFactoryInterface;

// If anything goes wrong retrieving the mvc factory something's gone terribly wrong so let the exception bubble up
/** @var MVCFactoryFactoryInterface $mvcFactoryFactory */
$mvcFactoryFactory = $app->bootComponent('com_content')->get(MVCFactoryFactoryInterface::class);

$model = $mvcFactoryFactory->createFactory($app)->createModel('Articles', 'Site', ['ignore_request' => true]);
$list = ArticlesLatestHelper::getList($params, $model);

require ModuleHelper::getLayoutPath('mod_articles_latest', $params->get('layout', 'default'));
