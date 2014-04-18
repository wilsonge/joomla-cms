<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Parser for language files
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       3.4
 */
interface JLanguageParserInterface
{
	/**
	 * Parses a language file.
	 *
	 * @param   array    $options   An array of options
	 *
	 * @return  array  The returning array containing:
	 *                 In the key strings: An array of containing the translated strings of the form
	 *                 constant => translated string
	 *                 In the key error files: An array containing the errors found parsing a file of
	 *                 the form filename => error message
	 *
	 * @since   3.4
	 */
	public function parse($options);
}