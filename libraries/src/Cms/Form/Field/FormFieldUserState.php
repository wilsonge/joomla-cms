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

use JFormHelper;
use JFormFieldPredefinedList;

JFormHelper::loadFieldClass('predefinedlist');

/**
 * Field to load a list of available users statuses
 *
 * @since  3.2
 */
class FormFieldUserState extends JFormFieldPredefinedList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.2
	 */
	protected $type = 'UserState';

	/**
	 * Available statuses
	 *
	 * @var  array
	 * @since  3.2
	 */
	protected $predefinedOptions = array(
		'0'  => 'JENABLED',
		'1'  => 'JDISABLED'
	);
}
