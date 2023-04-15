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
JDate::$format = 'd M Y';
?>
<style>
.icon-48-packs {
	background-image: url("components/com_cobalt/images/titles/packs.png");
}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_cobalt&view=packs');?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%" class="nowrap center">
					<?php echo JText::_('CNUM'); ?>
				</th>
				<th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
				<th width="1%" class="nowrap center">
					<?php echo JText::_('CSECTIONS'); ?>
				</th>
				<th class="title" class="nowrap center">
					<?php echo JHtml::_('grid.sort',  'CNAME', 'name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('Key'); ?>
				</th>
				<!-- <th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort',  'CCREATED', 'ctime', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort',  'CMTIME', 'mtime', $listDirn, $listOrder); ?>
				</th>-->
				<th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort',  'CBTIME', 'btime', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'CVERSION', 'version', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('Build'); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('Dowload'); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('Size'); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php

		foreach($this->items as $i => $item):

			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="nowrap center">
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td class="nowrap center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td nowrap="nowrap">
					<a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=packsections&filter_pack='.$item->id)?>">
						<?php echo JText::_('CSECTIONS');?>
					</a>
					<span class="badge badge-success"><?php echo $item->secnum?></span>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_cobalt&task=pack.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a>
				</td>
				<td class="nowrap center">
					<?php echo $item->key; ?>
				</td>
				<!-- <td class="nowrap center small">
					<?php echo JDate::getInstance($item->ctime); ?>
				</td>
				<td class="nowrap center small">
					<?php echo JDate::getInstance($item->mtime); ?>
				</td>-->
				<td class="nowrap center small">
					<?php echo $item->btime != '0000-00-00 00:00:00' ? JDate::getInstance($item->btime) : JText::_('CNEVER'); ?>
				</td>
				<td nowrap="nowrap">
					 <span class="badge badge-success">8.<?php echo $item->version; ?></span>
				</td>
				<td nowrap="nowrap" align="center">
					<a class="btn btn-primary btn-mini" href="<?php echo JRoute::_('index.php?option=com_cobalt&task=pack.build&pack_id='.$item->id)?>"><?php echo JText::_('CBUILD')?></a>
				</td>
				<td>
					<?php echo $item->download; ?>
				</td>
				<td nowrap="nowrap">
					<span class="badge badge-info"><?php echo $item->size; ?></span>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>