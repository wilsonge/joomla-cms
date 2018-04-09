<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Categories\Exception;

defined('JPATH_PLATFORM') or die;

/**
 * Exception class defining an error for a missing category
 *
 * @since  __DEPLOY_VERSION__
 */
class MissingCategoryException extends \InvalidArgumentException
{
	/**
	 * Constructor
	 *
	 * @param   string      $message   The Exception message to throw.
	 * @param   integer     $code      The Exception code.
	 * @param   \Exception  $previous  The previous exception used for the exception chaining.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($message = '', $code = 404, \Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
