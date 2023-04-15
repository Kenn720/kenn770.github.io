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
$user	= JFactory::getUser();
$userId	= $user->get('id');
JHtml::_('dropdown.init');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

?>
<style>
.has-context .btn-group {
	margin-bottom: -3px;
	margin-top: -3px;
}
</style>
<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="input-append btn-group pull-left">
			<input type="text" placeholder="<?php echo JText::_('CFILTER_SEARCH_SECTIONSDESC'); ?>" style="margin-left: 5px;" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />

			<button rel="tooltip" class="btn" type="submit" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button rel="tooltip" class="btn" type="button" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>

		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Cobalt.orderTable('<?php echo $listOrder?>')">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
				<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
				<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Cobalt.orderTable('<?php echo $listOrder?>')">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
			</select>
		</div>
	</div>




	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<?php echo JText::_('CNUM'); ?>
				</th>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'CSECTIONNAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="1%"></th>
				<th width="5%">
					<?php echo JText::_('CRECORDS'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'f.ordering');
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			$canChange	= true;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'sections.', $canChange);?>
				</td>
				<td class="has-context">
					<div class="pull-left">
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'sections.', $canCheckin); ?>
						<?php endif; ?>
						<a href="<?php echo JRoute::_('index.php?option=com_cobalt&task=section.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
					</div>
					<div class="pull-left">
						<?php
							// Create dropdown items
							JHtml::_('dropdown.edit', $item->id, 'section.');
							JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_DELETE'), 'javascript:void(0)', 'onclick="if(!confirm(\''.JText::_('C_TOOLBAR_CONFIRMDELET').'\')){return;}listItemTask(\'cb'.$i.'\',\'sections.delete\')"');
							if ($item->published) :
								JHtml::_('dropdown.unpublish', 'cb' . $i, 'sections.');
							else :
								JHtml::_('dropdown.publish', 'cb' . $i, 'sections.');
							endif;

							if ($item->checked_out) :
								JHtml::_('dropdown.divider');
								JHtml::_('dropdown.checkin', 'cb' . $i, 'sections.');
							endif;

							JHtml::_('dropdown.divider');
							JHtml::_('dropdown.addCustomItem', JText::_('C_MANAGE_CATS').' <span class="badge'.($item->categories ? ' badge-success' : NULL).'">'.$item->categories.'</span>', JRoute::_('index.php?option=com_cobalt&view=categories&section_id='.$item->id));

							echo JHtml::_('dropdown.render');
							?>
					</div>
				</td>
				<td nowrap="nowrap">
					<a rel="tooltip" data-original-title="<?php echo JText::_('CCATEGOY_MANAGE');?>" href="<?php echo JRoute::_('index.php?option=com_cobalt&view=categories&section_id='.$item->id)?>">
						<?php echo JText::_('CCATEGORIES');?>
					</a>
					<span class="badge<?php echo ($item->fieldnum ? ' badge-success' : null)?>"><?php echo $item->fieldnum; ?></span>
				</td>
				<td class="center">
					<span class="badge <?php echo ($item->records ? ' badge-info' : NULL)?>"><?php echo $item->records?></span>
				</td>
				<td class="center">
					<?php echo $item->language; ?>
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