<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user = JFactory::getUser();
$userId = $user->get('id');
$section = $this->section_id;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$ordering = ($listOrder == 'ordering');
$saveOrder = $listOrder == 'ordering';
$saveOrder 	= ($listOrder == 'ordering' && $listDirn == 'asc');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_cobalt&task=usercategories.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
?>
<style>
<!--
a.saveorder {
	width: 16px;
	height: 16px;
	display: block;
	overflow: hidden;
	background: url('<?php echo HTMLFormatHelper::icon('disk.png');  ?>') no-repeat;
	float: right;
	margin-right: 8px;
}

a.saveorder.inactive {
//	background-position: 0 -16px;
}
-->
</style>

<div class="page-header"><h1><?php echo JText::sprintf('CSECTIONCATS', $this->section->name);?></h1></div>
<form action="<?php echo JRoute::_('index.php?option=com_cobalt&view=categories&section_id='.$section);?>" method="post" name="adminForm" id="adminForm">
	<div class="btn-toolbar clearfix">
		<div class="pull-right">
			<button style="float:left;" type="button" class="btn" onclick="location.href = '<?php echo JRoute::_(Url::records($this->section, JFactory::getApplication()->input->getInt('category_id'), $user->get('id')));?>'">
				<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
					<?php echo JText::_('CBACKTOSECTION'); ?>
			</button>
			<button type="button" class="btn" onclick="Joomla.submitbutton('usercategory.add');">
				<?php echo HTMLFormatHelper::icon('plus-button.png');  ?>
				<?php echo JText::_('CADDNEW'); ?>
			</button>
			<?php if(count($this->items)) :?>
			<button type="button" class="btn" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('CMAKESELECTION')?>');}else{Joomla.submitbutton('usercategories.publish');}">
				<?php echo HTMLFormatHelper::icon('tick-button.png');  ?>
				<?php echo JText::_('CPUB'); ?>
			</button>
			<button type="button" class="btn" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('CMAKESELECTION')?>');}else{Joomla.submitbutton('usercategories.unpublish')};">
				<?php echo HTMLFormatHelper::icon('minus-button.png');  ?>
				<?php echo JText::_('CUNPUB'); ?>
			</button>
			<button type="button" class="btn btn-danger"
				onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('CMAKESELECTION')?>');}else{if(!confirm('<?php echo JText::_('CSURE')?>')){return;}Joomla.submitbutton('usercategories.delete')};">
				<?php echo HTMLFormatHelper::icon('cross-button.png');  ?>
				<?php echo JText::_('CDELETE'); ?>
			</button>
			<?php endif; ?>
		</div>
	</div>
<?php if(!count($this->items)) :?>
    <div class="alert">
	    <?php echo JText::_('CADDNEWCATEGORY');?>
    </div>
<?php else:?>
	<table class="table" id="categoryList">
		<thead>
			<th width="1%">
				<?php echo JText::_('#'); ?>
			</th>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
			</th>
			<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?>
			</th>
			
			<th width="1%">
				<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
			</th>
		</thead>
		<tbody>
			<?php
			$originalOrders = array();
			foreach($this->items as $i => $item):
				$orderkey   = $item->ordering;
				$canEdit = true; //$user->authorise('core.edit',			$section.'.usercategory.'.$item->id);
				$canCheckin = true; //$user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn = true; //$user->authorise('core.edit.own',		$section.'.usercategory.'.$item->id) && $item->user_id == $userId;
				$canChange = true; //$user->authorise('core.edit.state',	$section.'.usercategory.'.$item->id) && $canCheckin;
				?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $user->id;?>" item-id="<?php echo $item->id?>" parents=""  level="">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel    = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
							<i class="icon-menu"></i>
						</span>

					<?php else : ?>
						<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
						<input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $orderkey;?>" />
					</td>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>				
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'usercategories.', $canChange);?>
				</td>
				<td>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=category&id='.$item->id.'&section_id='.$section);?>">
							<?php echo $this->escape($item->name); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
				</td>
				
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="pagination pull-right">
		<?php echo $this->pagination->getPagesCounter(); ?>
		<?php echo $this->pagination->getLimitBox();?>
	</div>

	<div class="pull-left pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
	
		<input type="hidden" name="section_id" value="<?php echo $section;?>" /> 
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="limitstart" value="0" />
		<input type="hidden" name="boxchecked" value="0" /> 
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
		<input type="hidden" name="filter_order_Dir"
			value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	
	
</form>
