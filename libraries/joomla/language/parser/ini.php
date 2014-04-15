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
 * Language parser for INI files
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       3.4
 */
class JLanguageParserIni implements JLanguageParserInterface
{
	/**
	 * Debug language, If true, highlights if string isn't found.
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $debug;

	/**
	 * List of language files that are in error state
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $errorfiles = array();

	/**
	 * Parses a xxx.ini language file.
	 *
	 * @param   string   $filename  The name of the file.
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
	public function parse($filename, $options)
	{
		$this->debug = $options['debug'];
		$lang = $options['lang'];

		if ($this->debug)
		{
			// Capture hidden PHP errors from the parsing.
			$php_errormsg = null;
			$track_errors = ini_get('track_errors');
			ini_set('track_errors', true);
		}

		$contents = file_get_contents($filename);
		$contents = str_replace('_QQ_', '"\""', $contents);
		$strings = @parse_ini_string($contents);

		if (!is_array($strings))
		{
			$strings = array();
		}

		if ($this->debug)
		{
			// Restore error tracking to what it was before.
			ini_set('track_errors', $track_errors);

			// Initialise variables for manually parsing the file for common errors.
			$blacklist = array('YES', 'NO', 'NULL', 'FALSE', 'ON', 'OFF', 'NONE', 'TRUE');
			$regex = '/^(|(\[[^\]]*\])|([A-Z][A-Z0-9_\-\.]*\s*=(\s*(("[^"]*")|(_QQ_)))+))\s*(;.*)?$/';
			$this->debug = false;
			$errors = array();

			// Open the file as a stream.
			$file = new SplFileObject($filename);

			foreach ($file as $lineNumber => $line)
			{
				// Avoid BOM error as BOM is OK when using parse_ini
				if ($lineNumber == 0)
				{
					$line = str_replace("\xEF\xBB\xBF", '', $line);
				}

				// Check that the key is not in the blacklist and that the line format passes the regex.
				$key = strtoupper(trim(substr($line, 0, strpos($line, '='))));

				// Workaround to reduce regex complexity when matching escaped quotes
				$line = str_replace('\"', '_QQ_', $line);

				if (!preg_match($regex, $line) || in_array($key, $blacklist))
				{
					$errors[] = $lineNumber;
				}
			}

			// Check if we encountered any errors.
			if (count($errors))
			{
				if (basename($filename) != $lang . '.ini')
				{
					$this->errorfiles[$filename] = $filename . JText::sprintf('JERROR_PARSING_LANGUAGE_FILE', implode(', ', $errors));
				}
				else
				{
					$this->errorfiles[$filename] = $filename . '&#160;: error(s) in line(s) ' . implode(', ', $errors);
				}
			}
			elseif ($php_errormsg)
			{
				// We didn't find any errors but there's probably a parse notice.
				$this->errorfiles['PHP' . $filename] = 'PHP parser errors :' . $php_errormsg;
			}

			$this->debug = true;
		}

		$return = array(
			'strings' => $strings,
			'errorfiles' => $this->errorfiles,
		);

		return $return;
	}
}