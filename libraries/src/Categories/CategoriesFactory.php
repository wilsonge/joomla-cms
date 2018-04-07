<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Categories;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Categories\Exceptions\CategoryAlreadySetException;
use Joomla\CMS\Categories\Exceptions\CategoryNotFoundException;

/**
 * Categories Factory Class.
 *
 * @since  __DEPLOY_VERSION__
 */
class CategoriesFactory
{
	/**
	 * @var    Categories
	 * @since  __DEPLOY_VERSION__
	 */
	protected $rootCategory = null;

	/**
	 * @var    Categories[]
	 * @since  __DEPLOY_VERSION__
	 */
	protected $categories = null;

	/**
	 * Get a category
	 *
	 * @param   string  $section The section name for the category (null loads the main category)
	 *
	 * @return  Categories
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  CategoryNotFoundException
	 */
	public function getCategory($section = null)
	{
		if ($section === null && $this->rootCategory)
		{
			return $this->rootCategory;
		}

		if ($section !== null && array_key_exists($section, $this->categories))
		{
			return $this->categories[$section];
		}

		throw new CategoryNotFoundException;
	}

	/**
	 * Sets a category into the factory (note once set it may not be overridden)
	 *
	 * @param   Categories  $category  The category to save in the factory
	 * @param   string      $section   The optional section name for the category
	 *
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  CategoryAlreadySetException
	 */
	public function setCategory(Categories $category, $section = null)
	{
		if ($section === null)
		{
			if ($this->rootCategory !== null)
			{
				throw new CategoryAlreadySetException('Root category already set in factory');
			}

			$this->rootCategory = $category;

			return;
		}

		if (array_key_exists($section, $this->categories))
		{
			throw new CategoryAlreadySetException(sprintf('Category for section %s already set in factory', $section));
		}

		$this->categories[$section] = $category;
	}
}
