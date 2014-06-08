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
class JViewCmshtml extends JViewCms
{
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
	 * @param   RendererInterface   $renderer  The renderer object. Defaults to JLayout if not set.
	 *
	 * @since   3.4
	 */
	public function __construct(JModelCmsInterface $model, RendererInterface $renderer = null)
	{
		// If we don't have a renderer use the JLayout renderer
		if (!$renderer)
		{
			$config = array();

			$renderer = new JRendererJlayout($config);
		}

		// Set the renderer.
		$this->setRenderer($renderer);

		parent::__construct($model);
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