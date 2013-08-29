<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$category_sidebar 		= new JLayoutFile('joomla.apps.category_sidebar_ul');
?>
<a class="transcode" href="<?php echo AppsHelper::getAJAXUrl('view=dashboard'); ?>">
	<img  class="com-apps-logo" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/logo.png" alt="Joomla Apps"/>
</a>
<div class="com-apps-sidebar sidebar-nav">
	<h3><?php echo JText::_('COM_APPS_CATEGORIES'); ?></h3>
	<div class="scroll-pane">
	<ul class="nav com-apps-list">
		<?php foreach ($displayData as $category) : ?>
			<li><a class="transcode<?php echo $category->active ? ' active' : ''; ?><?php echo $category->selected ? ' selected' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$category->id}"); ?>"><?php echo $category->name; ?></a>
			<?php if (count($category->children)) : ?>
				<?php echo $category_sidebar->render($category->children); ?>
			<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	</div>
</div>
