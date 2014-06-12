<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

/**
 * Mustache class for rendering output.
 *
 * @since  1.0
 */
class Mustache extends \Mustache_Engine implements RendererInterface
{
	/**
	 * Render and return compiled data.
	 *
	 * @param   string  $template  The template file name
	 * @param   array   $data      The data to pass to the template
	 *
	 * @return  string  Compiled data
	 *
	 * @since   1.0
	 */
	public function render($template, array $data = array())
	{
		return parent::render($template, $data);
	}
}
