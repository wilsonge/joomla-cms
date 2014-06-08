<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Renderer
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Renderer\RendererInterface;

/**
 * JLayout class for rendering output.
 *
 * @since  3.4
 */
class JRendererJlayout implements RendererInterface
{
	/**
	 * The list of paths for layouts to be searched
	 *
	 * @var    array
	 * @since  3.4
	 */
	private $paths = array();

	/**
	 * Public constructor
	 *
	 * @param  array  $config  An array of configuration options
	 *
	 * @since  3.4
	 */
	public function __constructor($config)
	{
		if (isset($config['paths']))
		{
			$this->paths = (array) $config['paths'];
		}
	}

	/**
	 * Render and return compiled data.
	 *
	 * @param   string  $template  The template file name
	 * @param   array   $data      The data to pass to the template
	 *
	 * @return  string  Compiled data
	 *
	 * @since   3.4
	 */
	public function render($template, array $data = array())
	{
		return $this->getLayout($template)->render($data);
	}

	/**
	 * Gets a JLayoutFile object for a given template path.
	 *
	 * @param   string  $template  The template file name
	 *
	 * @return  JLayoutFile  The JLayoutFile object
	 *
	 * @since   3.4
	 */
	private function getLayout($template)
	{
		$layout = new JLayoutFile($template);

		if (!empty($this->paths))
		{
			$layout->setIncludePaths($this->paths);
		}

		return $layout;
	}
}
