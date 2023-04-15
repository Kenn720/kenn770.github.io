<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$user	= JFactory::getUser();
$userId	= $user->get('id');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="input-append btn-group pull-left">
			<input type="text" placeholder="<?php echo JText::_('CFILTER_SEARCH_DESC'); ?>" style="margin-left: 5px;" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />

			<button rel="tooltip" class="btn tip" type="submit" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button rel="tooltip" class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
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
	<br />

   <table class="table table-hover" id="articleList">
		<thead>
			<tr>
				<th width="1%">
					<?php echo JText::_('CNUM'); ?>
				</th>
				<th width="1%">
					 <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',  'CVOTE', 'a.vote',     $listDirn, $listOrder ); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',  'CTYPE', 'a.ref_type',     $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort',  'CARTICLE', 'r.title',   $listDirn, $listOrder ); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort',  'CUSER', 'u.username',$listDirn, $listOrder  ); ?>
				</th>
				<th width="8%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',  'CVOTED', 'a.ctime',   $listDirn, $listOrder  ); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',  'ID',    'a.id',      $listDirn, $listOrder  ); ?>
				</th>
				</tr>
		</thead>

		<tbody>
		<?php foreach ($this->items as $i => $item) :?>
			<tr class="row<?php echo $i % 2; ?>">

				<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>

				<td><?php echo JHTML::_('grid.id', $i, $item->id ); ?></td>

				<td>
					<small>
						<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYVOTE');?>" onclick="Cobalt.setAndSubmit('filter_votes', '<?php echo $item->vote?>')">
							<?php echo $this->escape($item->vote); ?>
						</a>
					</small>
				</td>

				<td>
					<small>
						<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYVOTETYPE');?>" onclick="Cobalt.setAndSubmit('filter_type', '<?php echo $item->ref_type?>')">
							<?php echo $this->escape($item->ref_type); ?>
						</a>
					</small>
				</td>

				<td>
					<a href="javascript:void(0);" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYRECORD');?>"onclick="document.getElementById('filter_search').value='record:<?php echo $item->record_id; ?>'; document.adminForm.submit();">
						<?php echo $item->record?>
					</a>
				</td>

				<td width="5%" class="nowrap">
					<small>
					<?php
					$user = JFactory::getUser($item->user_id);
					$link = 'index.php?option=com_users&task=edit&cid[]='.$user->get('id');
					if(is_dir(JPATH_ADMINISTRATOR. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_juser'))
						$link = 'index.php?option=com_juser&task=edit&cid[]='.$user->get('id');
					?>
					<?php if($user->get('username')):?>
							<?php echo JHtml::link('javascript:void(0);', $user->get('username'), array('rel' => "tooltip", 'data-original-title' => JText::_('CFILTERBYUSER'), 'onclick' => 'document.getElementById(\'filter_search\').value=\'user:'.$item->user_id.'\'; document.adminForm.submit();'))?>
							<?php //echo JHtml::_('ip.block_user', $item->user_id, $item->id);?>
					<?php else: ?>
						<?php echo $item->username ? $item->username  ." (<a href=\"javascript:void(0);\" rel=\"tooltip\" data-original-title=\"".JText::_('CFILTEREMAIL')."\" onclick=\"document.getElementById('filter_search').value='email:{$item->useremail}'; document.adminForm.submit();\">{$item->useremail}</a>) " : Jtext::_('CANONYMOUS')?>
					<?php endif;?>

					<?php if($item->ip): ?>
						<div>
							<?php echo JHtml::_('ip.country', $item->ip);?>
							<?php echo JHTML::link('javascript:void(0);' ,$item->ip, array('rel' => "tooltip", 'data-original-title' => JText::_('CFILTERBYIP'), 'onclick' => 'document.getElementById(\'filter_search\').value=\'ip:'.$item->ip.'\'; document.adminForm.submit();')); ?>
							<?php //echo JHtml::_('ip.block_ip', $item->ip, $item->id);?>
						</div>
					<?php endif; ?>
					</small>
				</td>

				<td align="center" class="nowrap">
					<small>
					<?php $data = new JDate( $item->ctime ); echo $data->format( JText::_('CDATE1' ) ); ?>
					</small>
				</td>

				<td align="center"><small><?php echo $item->id; ?></small></td>
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