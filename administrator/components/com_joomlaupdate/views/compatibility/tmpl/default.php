<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
<h3><?php echo JText::_('COM_JOOMLAUPDATE_INSTL_PRECHECK_RECOMMENDED_SETTINGS_TITLE'); ?></h3>
<div id="config-document">
	<div id="page-not_compatibile" class="tab">
		<div class="noshow">
			<div class="width-100">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th width="280px"><?php echo JText::_('COM_JOOMLAUPDATE_EXTENSION_NAME'); ?>
							</th>
							<th><?php echo JText::_('COM_JOOMLAUPDATE_INSTL_PRECHECK_ACTUAL'); ?>
							</th>
							<th><?php echo JText::_('COM_JOOMLAUPDATE_INSTL_PRECHECK_RECOMMENDED'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php if(!empty($this->items)){ ?>
					<?php foreach($this->items as $setting){ ?>
						<tr>
							<td><?php echo $setting->name; ?>
							</td>
							<td><span class="label label-important"><?php echo $setting->version; ?>
							</span></td>
							<td><span class="label label-success disabled"><?php echo $setting->min_compatible_version; ?>
							</span></td>
						</tr>
						<?php } ?>
						<?php }else{ ?>
						<tr>
							<td colspan="2"><?php echo JText::_('COM_JOOMLAUPDATE_NO_DATA_AVAILABLE'); ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
