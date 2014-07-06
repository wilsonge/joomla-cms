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
 * Legacy class for rendering output. Allows the use of $this->
 * and load template to allow people to transition to JLayouts
 * whilst still using the new MVC
 *
 * @since       3.4
 * @deprecated  3.4  Use JRendererJlayout to render output.
 */
class JRendererLegacy implements RendererInterface
{
	/**
	 * The set of search directories for resources (templates)
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $path = array('template' => array(), 'helper' => array());

	/**
	 * Layout extension
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $layoutExt = 'php';

	/**
	 * A priority queue of paths
	 *
	 * @var    SplPriorityQueue
	 * @since  3.4
	 */
	public $paths = null;

	/**
	 * An application object
	 *
	 * @var    JApplicationCms
	 * @since  3.4
	 */
	protected $app = null;

	/**
	 * Method to instantiate the view.
	 *
	 * @param   SplPriorityQueue  $paths   The priority queue of paths.
	 * @param   JApplicationCms   $config  An application object
	 *
	 * @since   3.4
	 */
	public function __construct(SplPriorityQueue $paths = null, JApplicationCms $app = null)
	{
		if (!$app)
		{
			$app = JFactory::getApplication();
		}

		$this->app = $app;

		if (!$paths)
		{
			$rootPath = $app->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE;
			$componentFolder = strtolower($app->input->get('option'));
			$viewName = strtolower($app->input->get('view'));

			// Add the default paths. Use exponential priorities to allow developers to
			// insert their own paths in between
			$paths = new SplPriorityQueue;
			$paths->insert($rootPath . '/templates/' . $app->getTemplate() . '/html/' . $componentFolder . '/' . $viewName, 100);
			$paths->insert($rootPath . '/components/' . $componentFolder . '/view/' . $viewName . '/tmpl', 10);
		}

		$this->paths = $paths;
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @see     JView::escape()
	 * @since   3.4
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
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
	public function render($layout, array $data = array())
	{
		// Set the base layout path
		$this->layout = $layout;

		// @todo this is dynamic and horrible
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->$key = $value;
			}
		}

		return $this->loadTemplate();
	}

	/**
	 * Get the base layout path.
	 *
	 * @return  string  The layout path
	 *
	 * @since   3.4
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 *
	 * @return  string  The output of the the template script.
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function loadTemplate($tpl = null)
	{
		$template = $this->app->getTemplate();
		$layout = $this->getLayout();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang = JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
		|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

		// Prevents adding path twise
		if (empty($this->path['template']))
		{
			// Adding template paths
			$this->paths->top();
			$defaultPath = $this->paths->current();
			$this->paths->next();
			$templatePath = $this->paths->current();
			$this->path['template'] = array($defaultPath, $templatePath);
		}

		// Load the template script
		jimport('joomla.filesystem.path');
		$filetofind = $this->createFileName('template', array('name' => $file));
		$pathToFile = JPath::find($this->path['template'], $filetofind);

		// If alternate layout can't be found, fall back to default layout
		if ($pathToFile == false)
		{
			$filetofind = $this->createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$pathToFile = JPath::find($this->path['template'], $filetofind);
		}

		// We can't find a template path - bail
		if ($pathToFile == false)
		{
			throw new RuntimeException(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
		}

		// Unset so as not to introduce into template scope
		unset($tpl);
		unset($file);

		// Never allow a 'this' property
		if (isset($this->this))
		{
			unset($this->this);
		}

		// Start capturing output into a buffer
		ob_start();

		// Include the requested template filename in the local scope
		// (this will execute the view logic).
		include $pathToFile;

		// Done with the requested template; get the buffer and
		// clear it.
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param   string  $type   The resource type to create the filename for
	 * @param   array   $parts  An associative array of filename information
	 *
	 * @return  string  The filename
	 *
	 * @since   3.2
	 */
	protected function createFileName($type, $parts = array())
	{
		$filename = '';

		switch ($type)
		{
			case 'template':
				$filename = strtolower($parts['name']) . '.' . $this->layoutExt;
				break;

			default:
				$filename = strtolower($parts['name']) . '.php';
				break;
		}

		return $filename;
	}

	public function getForm()
	{
		return $this->form;
	}

	public function get($value, $default = null)
	{
		if (isset($this->$value))
		{
			return $this->$value;
		}

		return $default;
	}
}
