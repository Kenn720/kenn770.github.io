<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('dropdown.init');
JHtml::_('behavior.modal', 'a.modal');

$user	= JFactory::getUser();
$userId	= $user->get('id');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo $this->action ?>" method="post" id="adminForm" name="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="input-append btn-group pull-left">
			<input type="text" placeholder="<?php echo JText::_('CFILTER_SEARCH_DESC'); ?>" style="margin-left: 5px;" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />

			<button rel="tooltip" class="btn" type="submit" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button rel="tooltip" class="btn" type="button" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>

			<?php if($this->state->get('filter.category', 0)):?>
			<button class="btn" onclick="this.form.submit()"><?php echo JText::_('CRESETCATFILTER');?></button>
			<?php endif;?>
		<input type="hidden" name="filter_category" id="filter_category" value="" />
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

	<div class="clearfix"> </div>

	<div class="alert alert-error hide" id="add-info">
		<?php echo JText::_('CWHYNOADDRECORD'); ?>
	</div>

	<table class="table table-hover" id="articleList">
		<thead>
			<th width="1%">
				<?php echo JText::_('CNUM'); ?>
			</th>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort',  'CTITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th width="1%">
				<?php echo JHtml::_('grid.sort',  'CTYPE', 't.name', $listDirn, $listOrder); ?>
			</th>
			<th width="1%">
				<?php echo JHtml::_('grid.sort',  'CSECTION', 's.name', $listDirn, $listOrder); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('CCATEGORY'); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'CAUTHOR', 'u.username', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'CCREATED', 'a.ctime', $listDirn, $listOrder); ?><br/>
				<?php echo JHtml::_('grid.sort',  'CEXPIRE', 'a.extime', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'CACCESS', 'a.access', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
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
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'records.', $canChange);?>
						<?php echo JHtml::_('records.featured', $item->featured, $i, $canChange); ?>
					</div>
				</td>
				<td class="nowrap has-context">
					<div class="pull-left">
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'records.', $canCheckin); ?>
						<?php endif; ?>
						<a title="<?php echo JText::_('CEDITRECORD');?>" rel="{handler: 'iframe', size: {x: 800, y: 600} }" class="modal" href="<?php echo JRoute::_('../index.php?option=com_cobalt&view=form&modal=1&tmpl=component&id='.(int) $item->id); ?>">
							<?php echo strip_tags($item->title); ?>
						</a><br/>
						<small>
							<?php echo JHtml::_('grid.sort',  'CHITS', 'a.hits', $listDirn, $listOrder); ?>
							<span class="badge"><small><?php echo $this->escape($item->hits); ?></small></span>

							<?php echo JHtml::_('grid.sort',  'CCOMMENTS', 'a.comments', $listDirn, $listOrder); ?>
							<a rel="tooltip" data-original-title="<?php echo JText::_('CSHOWRECORDCOMMENTS');?>" href="<?php echo JRoute::_('index.php?option=com_cobalt&view=comments&filter_search=record:'.$item->id);?>" class="badge badge-info"><small><?php echo $this->escape($item->comments); ?></small></a>

							<?php echo JHtml::_('grid.sort',  'CVOTES', 'a.votes', $listDirn, $listOrder); ?>
							<a rel="tooltip" data-original-title="<?php echo JText::_('CSHOWRECORDVOTES');?>" href="<?php echo JRoute::_('index.php?option=com_cobalt&view=votes&filter_search=record:'.$item->id);?>" class="badge badge-info"><small><?php echo $this->escape($item->votes); ?></small></a>

							<?php echo JHtml::_('grid.sort',  'CFAVORITED', 'a.favorite_num', $listDirn, $listOrder); ?>
							<span class="badge"><small><?php echo $this->escape($item->favorite_num); ?></small></span>
						</small>
					</div>
					<div class="pull-left">
						<?php
							// Create dropdown items
							JHtml::_('dropdown.addCustomItem', JText::_('CCOPY'), 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.copy\')"');
							if (!$item->featured) :
								JHtml::_('dropdown.addCustomItem', JText::_('CFEATURE'), 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.featured\')"');
							endif;

							JHtml::_('dropdown.divider');

							if ($item->published) :
								JHtml::_('dropdown.unpublish', 'cb' . $i, 'records.');
							else :
								JHtml::_('dropdown.publish', 'cb' . $i, 'records.');
							endif;

							if ($item->published == 2) :
								JHtml::_('dropdown.unarchive', 'cb' . $i, 'records.');
							else :
								JHtml::_('dropdown.archive', 'cb' . $i, 'records.');
							endif;

							if ($item->checked_out) :
								JHtml::_('dropdown.divider');
								JHtml::_('dropdown.checkin', 'cb' . $i, 'records.');
							endif;

							JHtml::_('dropdown.divider');

							JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_CTIME'), 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_ctime\')"');
							JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_MTIME'), 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_mtime\')"');
							JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_EXTIME'), 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_extime\')"');

							JHtml::_('dropdown.divider');
							if ($item->hits):
								JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_HITS')." <span class=\"badge badge-info\">{$item->hits}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_hits\')"');
							endif;
							if ($item->comments):
								JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_COOMENT')." <span class=\"badge badge-info\">{$item->comments}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_com\')"');
							endif;
							if ($item->votes):
								JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_RATING')." <span class=\"badge badge-info\">{$item->votes}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_vote\')"');
							endif;
							if ($item->favorite_num):
								JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_FAVORIT')." <span class=\"badge badge-info\">{$item->favorite_num}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb'.$i.'\',\'records.reset_fav\')"');
							endif;

							echo JHtml::_('dropdown.render');
							?>
					</div>
				</td>
				<td>
					<small>
						<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYTYPE');?>" onclick="Cobalt.setAndSubmit('filter_type', <?php echo $item->type_id?>)">
							<?php echo $this->escape($item->type_name); ?>
						</a>
					</small>
				</td>
				<td>
					<small>
						<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYSECTION');?>" onclick="Cobalt.setAndSubmit('filter_section', <?php echo $item->section_id?>)">
							<?php echo $this->escape($item->section_name); ?>
						</a>
					</small>
				</td>
				<td nowrap="nowrap">
					<small>
						<?php foreach ($item->categories AS $key => $category):?>
							<a rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYCATEGORY');?>" href="#" onclick="Cobalt.setAndSubmit('filter_category', <?php echo $key;?>);"><?php echo $category;?></a><br/>
						<?php endforeach;?>
				</td>
				<td nowrap="nowrap">
					<small>
						<?php echo JHtml::link('javascript:void(0);', ($item->username ? $item->username : Jtext::_('CANONYMOUS')) , array('rel' => "tooltip", 'data-original-title' => JText::_('CFILTERBYUSER'), 'onclick' => 'Cobalt.setAndSubmit(\'filter_search\', \'user:'.$item->user_id.'\');'))?>
						<?php if($item->ip): ?>
							<div>
								<?php echo JHtml::_('ip.country', $item->ip);?>
								<?php echo JHTML::link('javascript:void(0);' ,$item->ip, array('rel' => "tooltip", 'data-original-title' => JText::_('CFILTERBYIP'), 'onclick' => 'Cobalt.setAndSubmit(\'filter_search\', \'ip:'.$item->ip.'\');')); ?>
							</div>
						<?php endif; ?>
					</small>
				</td>
				<td nowrap="nowrap">
					<small>
						<?php $data = new JDate($item->ctime); echo $data->format(JText::_('CDATE1')); ?><br />
						<?php if($item->extime == '0000-00-00 00:00:00'): ?>
							<span style="color: green"><?php echo JText::_('CNEVER') ?></span>
						<?php else: ?>
							<?php $extime = new JDate($item->extime); ?>
							<span style="color: <?php echo ($extime->toUnix() <= time() ? 'red' : 'green') ?>">
							<?php echo $extime->format(JText::_('CDATE1')); ?>
							</span>
						<?php endif; ?>
					</small>
				</td>
				<td align="center">
					<small>
						<?php echo $this->escape($item->access_title); ?>
					</small>
				</td>
				<td class="center">
					<small>
						<?php echo (int) $item->id; ?>
					</small>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>