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
class JRendererLegacy extends JViewLegacy implements RendererInterface
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.<br/>
	 *                          name: the name (optional) of the view (defaults to the view class name suffix).<br/>
	 *                          charset: the character set to use for display<br/>
	 *                          escape: the name (optional) of the function to use for escaping strings<br/>
	 *                          base_path: the parent path (optional) of the views directory (defaults to the component folder)<br/>
	 *                          template_plath: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
	 *                          helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br/>
	 *                          layout: the layout (optional) to use to display the view<br/>
	 *
	 * @since   3.4
	 */
	public function __construct($config = array())
	{
		$this->_name = $config['view'];

		parent::__construct($config);
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
		$this->setLayout($layout);

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
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'ul.name' => JText::_('COM_CONTACT_FIELD_LINKED_USER_LABEL'),
			'a.featured' => JText::_('JFEATURED'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
