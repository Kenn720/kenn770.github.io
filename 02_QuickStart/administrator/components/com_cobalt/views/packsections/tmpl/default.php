<?php 
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<form action="<?php echo JRoute::_('index.php?option=com_cobalt&view=packsections');?>" method="post" name="adminForm" id="adminForm">
<!-- 	<div class="row-fluid">
		<div class="span10 form-horizontal"> -->
		
		
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<?php echo JText::_('CNUM'); ?>
				</th>
				<th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" /></th>
				
				<th class="title">
					<?php echo JHtml::_('grid.sort',  'Name', 'name', $listDirn, $listOrder); ?>
				</th>

				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php

		foreach($this->items as $i => $item):
			
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td nowrap="nowrap">
					<a href="<?php echo JRoute::_('index.php?option=com_cobalt&task=packsection.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="pack_id" value="<?php echo $this->state->get('pack'); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>