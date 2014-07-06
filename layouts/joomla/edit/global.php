<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$app = JFactory::getApplication();
$input = $app->input;
$component = $input->getCmd('option', 'com_content');

if ($component == 'com_categories')
{
	$extension	= $input->getCmd('extension', 'com_content');
	$parts		= explode('.', $extension);
	$component	= $parts[0];
}

// @deprecated  Injecting JViewLegacy directly into a JLayout is deprecated
//              you should just insert the JForm, fields and hidden fields
//              as part of an array of data
if ($displayData instanceof JViewLegacy)
{
	$form = $displayData->getForm();

	$fields = $displayData->get('fields') ?: array(
		array('category', 'catid'),
		array('parent', 'parent_id'),
		'tags',
		array('published', 'state', 'enabled'),
		'featured',
		'sticky',
		'access',
		'language',
		'note',
		'version_note'
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
			array('category', 'catid'),
			array('parent', 'parent_id'),
			'tags',
			array('published', 'state', 'enabled'),
			'featured',
			'sticky',
			'access',
			'language',
			'note',
			'version_note'
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

// Multilanguage check:
/*if (!JLanguageMultilang::isEnabled())
{
	$hiddenFields[] = 'language';
}*/

$saveHistory = JComponentHelper::getParams($component)->get('save_history', 0);

if (!$saveHistory)
{
	$hiddenFields[] = 'version_note';
}

$html = array();
$html[] = '<fieldset class="form-vertical">';

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

			$html[] = $form->renderField($f);
			break;
		}
	}
}

$html[] = '</fieldset>';

echo implode('', $html);
