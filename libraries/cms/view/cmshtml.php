<?php
/**
 * @package     Joomla.Cms
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Renderer\RendererInterface;

/**
 * Prototype JView class.
 *
 * @package     Joomla.Libraries
 * @subpackage  View
 * @since       3.4
 */
class JViewCmshtml implements JView
{
	/**
	 * The data array to pass to the renderer engine
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $data = array();

	/**
	 * Key of the default model in the models array
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $defaultModel;

	/**
	 * The view layout.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $layout = 'default';

	/**
	 * Associative array of model objects $models[$name]
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $models = array();

	/**
	 * The name of the view.
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $name;

	/**
	 * The renderer object
	 *
	 * @var    RendererInterface
	 * @since  3.4
	 */
	protected $renderer;

	/**
	 * Method to instantiate the view.
	 *
	 * @param   JModelCmsInterface  $model     The model object.
	 * @param   RendererInterface   $renderer  The renderer object.
	 *
	 * @since   3.4
	 */
	public function __construct(JModelCmsInterface $model, RendererInterface $renderer)
	{
		// Setup dependencies.
		$this->setRenderer($renderer);
		$this->setModel($model, true);
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @since   3.4
	 */
	public function escape($output)
	{
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Retrieves the data array from the default model
	 *
	 * @return  array
	 *
	 * @since   3.4
	 */
	public function getData()
	{
		return $this->getModel()->getData();
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 *
	 * @since   3.4
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Method to get the model object
	 *
	 * @param   string $name The name of the model (optional)
	 *
	 * @return  JModelCmsInterface
	 *
	 */
	public function getModel($name = null)
	{
		if ($name === null)
		{
			$name = $this->defaultModel;
		}

		return $this->models[$name];
	}

	/**
	 * Method to get the view name
	 *
	 * The model name by default parsed using the classname
	 *
	 * @return  string  The name of the model
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$className = get_class($this);
			$viewPos = strpos($className, 'View');

			if ($viewPos === false)
			{
				throw new RuntimeException(JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}

			$lastPart = substr($className, $viewPos + 4);
			$pathParts = explode(' ', JStringNormalise::fromCamelCase($lastPart));

			if (!empty($pathParts[1]))
			{
				$this->name = strtolower($pathParts[0]);
			}
			else
			{
				$this->name = strtolower($lastPart);
			}
		}

		return $this->name;
	}

	/**
	 * Retrieves the renderer object
	 *
	 * @return  RendererInterface
	 *
	 * @since   3.4
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.4
	 * @throws  RuntimeException
	 */
	public function render()
	{
		return $this->getRenderer()->render($this->getLayout(), $this->getData());
	}

	/**
	 * Sets the data array
	 *
	 * @param   array  $data  The data array.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   3.4
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  $this  Method supports chaining.
	 *
	 * @since   3.4
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Method to add a model to the view.  We support a multiple model single
	 * view system by which models are referenced by class name.
	 *
	 * @param   JModelCmsInterface   $model   The model to add to the view.
	 * @param   boolean              $default Is this the default model?
	 *
	 * @return  void
	 */
	public function setModel(JModelCmsInterface $model, $default = false)
	{
		$name = strtolower($model->getName());
		$this->models[$name] = $model;

		if ($default)
		{
			$this->defaultModel = $name;
		}
	}

	/**
	 * Sets the renderer object
	 *
	 * @param   RendererInterface  $renderer  The renderer object.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   3.4
	 */
	public function setRenderer(RendererInterface $renderer)
	{
		$this->renderer = $renderer;

		return $this;
	}
}