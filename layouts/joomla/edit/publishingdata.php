<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// @deprecated  Injecting JViewLegacy directly into a JLayout is deprecated
//              you should just insert the JForm, fields and hidden fields
//              as part of an array of data
if ($displayData instanceof JViewLegacy)
{
	$form = $displayData->getForm();

	$fields = $displayData->get('fields') ?: array(
		'publish_up',
		'publish_down',
		array('created', 'created_time'),
		array('created_by', 'created_user_id'),
		'created_by_alias',
		array('modified', 'modified_time'),
		array('modified_by', 'modified_user_id'),
		'version',
		'hits',
		'id'
	);

	$hiddenFields = $displayData->get('hidden_fields') ?: array();
}
else
{
	$form = $displayData['form'];

	if (!empty($displayData['fields']))
	{
		$fields = $displayData['fields'];
	}
	else
	{
		$fields = array(
			'publish_up',
			'publish_down',
			array('created', 'created_time'),
			array('created_by', 'created_user_id'),
			'created_by_alias',
			array('modified', 'modified_time'),
			array('modified_by', 'modified_user_id'),
			'version',
			'hits',
			'id'
		);
	}

	if (!empty($displayData['hidden_fields']))
	{
		$hiddenFields = $displayData['hidden_fields'];
	}
	else
	{
		$hiddenFields = array();
	}
}

foreach ($fields as $field)
{
	$field = is_array($field) ? $field : array($field);
	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			echo $form->renderField($f);
			break;
		}
	}
}
