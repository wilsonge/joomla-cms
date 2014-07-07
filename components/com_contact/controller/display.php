<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       3.4
 */
class ContactControllerDisplay extends JControllerDisplay
{
	/**
	 * Method to get a view, initiating it if it does not already exist.
	 * This method assumes auto-loading format is $prefix . 'View' . $name . $type
	 * The
	 *
	 * @param   JModelCmsInterface  $model   The model to be injected
	 * @param   string              $prefix  Option prefix exp. com_content
	 * @param   string              $name    Name of the view folder exp. articles
	 * @param   string              $type    Name of the file exp. html = html.php
	 * @param   array               $config  An array of config options
	 *
	 * @throws  RuntimeException
	 * @return  JViewCms
	 */
	protected function getView(JModelCmsInterface $model, $prefix = null, $name = null, $type = null, $config = array())
	{
		// Get the prefix if not given
		if (is_null($prefix))
		{
			$prefix = $this->getPrefix();
		}

		// Get the name if not given
		if (is_null($name))
		{
			$name = $this->config['subject'];
		}

		$this->config['view'] = $name;

		// Get the document type
		if (is_null($type))
		{
			$type   = $this->doc->getType();
		}

		$class = ucfirst($prefix) . 'View' . ucfirst($name) . ucfirst($type);

		if ($this->view instanceof $class)
		{
			return $this->view;
		}

		// If a custom class doesn't exist fall back to the Joomla class if it exists
		if (!class_exists($class))
		{
			$joomlaClass = 'JView' . ucfirst($type) . 'Cms';

			if (!class_exists($joomlaClass))
			{
				// @todo convert to a proper language string
				throw new RuntimeException(JText::sprintf('The view %s could not be found', $class));
			}

			// We've found a relevant Joomla class - use it.
			$class = $joomlaClass;
		}

		// The Html view must have a renderer object injected into it.
		// So initalise it separately
		if(strtolower($type) != 'html')
		{
			$view = new $class($model, $this->config);
		}
		else
		{
			/**
			$renderer = $this->getRenderer();

			// Initialise the view class
			$view = new $class($model, $renderer, $this->config);
			**/

			// Register the layout paths for the view
			$paths = new SplPriorityQueue;

			if ($this->app->isAdmin())
			{
				$paths->insert(JPATH_ADMINISTRATOR . '/components/' . $this->config['option'] . '/view/' . $this->viewName . '/tmpl', 1);
			}
			else
			{
				$paths->insert(JPATH_BASE . '/components/' . $this->config['option'] . '/view/' . $this->viewName . '/tmpl', 1);
			}

			$view = new $class($model, $paths);


			// If in html view then we set the layout
			$layoutName   = $this->input->getWord('layout', 'default');
			$view->setLayout($layoutName);
		}

		// Deal with json and hypermedia if requested
		if (strtolower($type) == 'json')
		{
			if (isset($this->config['useHypermedia']) && $this->config['useHypermedia'])
			{
				$this->doc->setMimeEncoding('application/hal+json');
				$view->useHypermedia = true;
			}
			else
			{
				$this->doc->setMimeEncoding('application/json');
			}
		}

		$this->view = $view;

		return $this->view;
	}
}
