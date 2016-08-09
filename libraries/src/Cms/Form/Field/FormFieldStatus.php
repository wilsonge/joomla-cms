<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Form\Field;

defined('JPATH_PLATFORM') or die;

use JFormHelper;
use JFormFieldPredefinedList;

JFormHelper::loadFieldClass('predefinedlist');

/**
 * Form Field to load a list of states
 *
 * @since  3.2
 */
class FormFieldStatus extends JFormFieldPredefinedList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $type = 'Status';

	/**
	 * Available statuses
	 *
	 * @var  array
	 * @since  3.2
	 */
	protected $predefinedOptions = array(
		'-2' =>	'JTRASHED',
		'0'  => 'JUNPUBLISHED',
		'1'  => 'JPUBLISHED',
		'2'  => 'JARCHIVED',
		'*'  => 'JALL'
	);
}
