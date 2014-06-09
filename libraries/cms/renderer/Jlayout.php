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
	 * The renderer default configuration parameters.
	 *
	 * @var    array
	 * @since  3.4
	 */
	private $config = array();

	/**
	 * Public constructor
	 *
	 * @param  array  $config  An array of configuration options
	 *
	 * @since  3.4
	 */
	public function __constructor($config)
	{
		// Find the root path - either site or administrator
		$rootPath = $app->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE;

		/**
		 * Get the component and view name
		 * 
		 * @todo Feels like there should be a better way of doing this
		 * maybe also check it in the config? But we should be independent of that
		 * in the renderer
		**/
		$input = JFactory::getApplication()->input;
		$componentFolder = strtolower($input->get('option'));
		$viewName = strtolower($input->get('view'));

		// Add the default paths
		$this->config['paths'] = array();
		$this->config['paths'][] = $rootPath . '/templates/html' . $componentFolder . '/' . $viewName;
		$this->config['paths'][] = $rootPath . '/components/' . $componentFolder . '/view/' . $viewName . '/tmpl';

		// Merge the config.
		$this->config = array_merge($this->config, $config);
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
			$layout->setIncludePaths($this->config['paths']);
		}

		return $layout;
	}
}
