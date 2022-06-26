<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Form\Field;

/**
 * Plugin Status field.
 *
 * @since  3.5
 */
class PluginstatusField extends PredefinedlistField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  3.5
     */
    public $type = 'Plugin_Status';

    /**
     * Available statuses
     *
     * @var  array
     * @since  3.5
     */
    protected $predefinedOptions = array(
        '0'  => 'JDISABLED',
        '1'  => 'JENABLED',
    );
}
