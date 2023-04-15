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

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$alert = JText::_('CMAKESELECTION');
$params = JComponentHelper::getParams('com_cobalt');
$back = NULL;
if(JFactory::getApplication()->input->getBase64('return'))
{
	$back = Url::get_back('return');
}
JHtml::_('dropdown.init');
?>
<div class="page-header">
	<h1>
		<?php echo JText::_('CMODERLIST');?>
	</h1>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_cobalt&view=moderators'); ?>" method="post" id="adminForm" name="adminForm">
	<br />
	<div class="controls controls-row">
		<div class="pull-right">
			<?php if($this->items && $this->state->get('filter.section')):?>
				<select name="filter_published" style="max-width:150px;" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0,'trash' => 0,'all' => 0,)), 'value', 'text', $this->state->get('filter.state'), true);?>
				</select>
			<?php endif;?>
			<select name="filter_section" style="max-width:150px;" onchange="this.form.submit()">
				<?php if($params->get('moderator', - 1) == $userId):?>
					<option value=""><?php echo JText::_('CSELECTSECTION');?></option>
				<?php endif;?>
				<?php echo JHtml::_('select.options', $this->filter_sections, 'value', 'text', $this->state->get('filter.section'), true);?>
			</select>
		</div>
		<?php if($this->state->get('filter.section')):?>
			<div class="input-append">
				<input class="span7" type="text" name="filter_search"	id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>"/>
				<button class="btn" type="submit" rel="tooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<?php echo HTMLFormatHelper::icon('magnifier.png');  ?>
				</button>
				<?php if($this->state->get('filter.search')) :?>
				<button class="btn<?php echo ($this->state->get('filter.search') ? ' btn-warning' : NULL); ?>" type="button"
					onclick="Cobalt.setAndSubmit('filter_search', '');" rel="tooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
					<?php echo HTMLFormatHelper::icon('eraser.png');  ?>
				</button>
				<?php endif; ?>
			</div>
		<?php endif;?>
	</div>

	<div class="btn-toolbar clearfix">
		<?php if($back):?>
			<button style="float:left;" type="button" class="btn" onclick="location.href = '<?php echo Url::get_back('return', $this->state->get('moderators.return'));?>'">
				<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
				<?php echo JText::_('CBACKTOSECTION'); ?>
			</button>
		<?php endif;?>

		<?php if($this->state->get('filter.section', false)):?>
			<div class="pull-right">
				<button type="button" class="btn" onclick="Joomla.submitbutton('moderator.add')">
					<?php echo HTMLFormatHelper::icon('plus.png');  ?>
					<?php echo JText::_('CNEW') ?>
				</button>
				<?php if($this->items):?>
					<button type="button" class="btn" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('<?php echo $alert;?>');}else{ Joomla.submitbutton('moderator.edit')}">
						<?php echo HTMLFormatHelper::icon('pencil.png');  ?>
						<?php echo JText::_('CEDIT') ?>
					</button>
					<button type="button" class="btn" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('<?php echo $alert;?>');}else{ Joomla.submitbutton('moderators.publish')}">
						<?php echo HTMLFormatHelper::icon('tick-circle.png');  ?>
						<?php echo JText::_('CPUB') ?>
					</button>
					<button type="button" class="btn" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('<?php echo $alert;?>');}else{ Joomla.submitbutton('moderators.unpublish')}">
						<?php echo HTMLFormatHelper::icon('cross-circle.png');  ?>
						<?php echo JText::_('CUNPUB') ?>
					</button>
					<button type="button" class="btn btn-danger" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('<?php echo $alert;?>');}else{ Joomla.submitbutton('moderators.delete')}">
						<?php echo HTMLFormatHelper::icon('minus-circle.png');  ?>
						<?php echo JText::_('CDELETE') ?>
					</button>
				<?php endif;?>
			</div>
		<?php endif;?>
	</div>
	<?php if($this->state->get('filter.section')):?>
		<?php if(count($this->items) > 0):?>
			<table class="table table-striped">
				<thead>
					<th width="1%">
						<?php echo JText::_('#'); ?>
					</th>
					<th width="1%"><input type="checkbox" name="checkall-toggle" value=""
						onclick="checkAll(this)" /></th>
					<th width="25px"></th>
					<th class="has-context">
						<?php echo JHtml::_('grid.sort',  'User', 'u.username', $listDirn, $listOrder); ?>
						<!--
						<div class="pull-left collapse">
							<div class="btn-group">
								<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-mini">
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li class=""><a>Ascending</a></li>
									<li class=""><a>Descending</a></li>
									<li class="divider"></li>
									<li class=""><a>
										<label class="checkbox">
											<input type="checkbox" /> Sergey
										</label>
										<label class="checkbox">
											<input type="checkbox" /> Test
										</label>
										<label class="checkbox">
											<input type="checkbox" /> John
										</label>
										<label class="checkbox">
											<input type="checkbox" /> Kit
										</label>
										</a>
									</li>
								</ul>
							</div>
						</div>
						-->
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  'Date', 'm.ctime', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  'State', 'm.published', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap">
						<?php echo JHtml::_('grid.sort',  'ID', 'm.id', $listDirn, $listOrder); ?>
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
						<td nowrap="nowrap"><img src="<?php echo CCommunityHelper::getAvatar($item->user_id, 25, 25); ?>"></td>
						<td>
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'moderators.', $canCheckin); ?>
							<?php endif; ?>

							<a href="<?php echo JRoute::_('index.php?option=com_cobalt&task=moderator.edit&id='.(int) $item->id); ?>">
								<?php echo CCommunityHelper::getName($item->user_id, $this->section_model->getItem($item->section_id), array('nohtml' => 1)); ?>
							</a>
							<?php if($item->icon && $item->icon != -1):?>
								<img src="<?php echo JURI::root(TRUE);?>/components/com_cobalt/images/moderator/<?php echo $item->icon; ?>" alt="" />
							<?php endif; ?>

							<?php if($item->description):?>
								<p><small>
									<?php echo $item->description;?>
								</small></p>
							<?php endif;?>
						</td>
						<td class="center" nowrap="nowrap">
							<?php echo JHtml::_('date', $item->ctime, 'd M Y');?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'moderators.', $canChange);?>
						</td>
						<td class="center">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div style="text-align: center;">
				<small>
					<?php if($this->pagination->getPagesCounter()):?>
						<?php echo $this->pagination->getPagesCounter(); ?>
					<?php endif;?>
					<?php echo str_replace('<option value="0">'.JText::_('JALL').'</option>', '', $this->pagination->getLimitBox());?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</small>
			</div>
			<div style="text-align: center;" class="pagination">
				<?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
			</div>
			<div class="clearfix"></div>
		<?php else:?>
			<div class="alert clearfix">
				<?php echo JText::_('CADDMODER');?>
			</div>
		<?php endif;?>


	<?php else:?>
		<div class="alert clearfix">
			<?php echo JText::_('CPLEASESELECTSECTION');?>
		</div>
	<?php endif;?>

	<input type="hidden" name="section_id" value="<?php echo $this->state->get('filter.section')?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>