<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Form\Field;

defined('JPATH_PLATFORM') or die;

use JHtml;
use JFormHelper;
use JFormFieldList;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla! CMS.
 *
 * @since  3.0
 */
class FormFieldModuletag extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $type = 'ModuleTag';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.0
	 */
	protected function getOptions()
	{
		$options = array();
		$tags = array('div', 'section', 'aside', 'nav', 'address', 'article');

		// Create one new option object for each tag
		foreach ($tags as $tag)
		{
			$tmp = JHtml::_('select.option', $tag, $tag);
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
