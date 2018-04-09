<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Extension\Service;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Categories\Categories;

/**
 * Access to component specific services.
 *
 * @since  __DEPLOY_VERSION__
 */
interface CategoryAwareInterface
{
	/**
	 * Returns the category service. If the service is not available
	 * null is returned.
	 *
	 * @param   array   $options  The options
	 * @param   string  $section  The section
	 *
	 * @return  Categories
	 *
	 * @see Categories::setOptions()
	 *
	 * @since  __DEPLOY_VERSION__
	 * @throws \Joomla\CMS\Component\Exception\MissingCategoryException
	 */
	public function getCategories(array $options = [], $section = ''): Categories;
}
