<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

if(!$this->items) return;

$params = $this->tmpl_params['list'];
JHtml::_('dropdown.init');
$k = 0;
$cols = $params->get('tmpl_params.columns', 3);
$span = array(1 => 12, 2 => 6, 3 => 4, 4 => 3, 6 => 2);
$desc_key = $this->field_keys_by_id[$params->get('tmpl_params.field_id_descr')];
$gall_key = $this->field_keys_by_id[$params->get('tmpl_params.field_id_gallery')];
?>
<style>
	#list-gallery h4 {
		margin-top: 0px;
	}
	#list-gallery .album-box {
		position: relative;
}
	#list-gallery .control-group {
		position: absolute;
		top: -11px;
		right: -9px;
}
.gall-description {
	max-height: 65px;
	overflow: hidden;
}
</style>
<div id="list-gallery">
<?php foreach ($this->items AS $item):?>
	
	<?php if($k % $cols == 0):?>
		<div class="row-fluid">
	<?php endif;?>
	
		<div class="span<?php echo $span[$cols]?> has-context">
			<div class="album-box <?php echo $params->get('tmpl_params.well') ? 'well well-small' : NULL;?>">
				<?php if($params->get('tmpl_core.item_title')):?>
					<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
						<div class="pull-left">
							<h4>
	
								<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
								<?php echo $item->title?>
								</a>
								<?php echo CEventsHelper::showNum('record', $item->id);?>
							</h4>
						</div>
					<?php endif;?>
					<?php if($this->user->get('id')):?>
						<div class="control-group">
							<div class="btn-group" style="display: none;">
								<?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
								<?php echo HTMLFormatHelper::follow($item, $this->section);?>
								<?php echo HTMLFormatHelper::repost($item, $this->section);?>
								<?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
								<?php if($item->controls):?>
									<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-mini">
										<?php echo HTMLFormatHelper::icon('gear.png');  ?>
									</a>
									<ul class="dropdown-menu">
										<?php echo list_controls($item->controls);?>
									</ul>
								<?php endif;?>
							</div>
						</div>
					<?php endif;?>
				<?php endif;?>
				<div class="clearfix"></div>
				
				<p><small>
					<?php if($params->get('tmpl_core.item_author_avatar')):?>
						<img alt="avatar" align="absmiddle" src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 22), $params->get('tmpl_core.item_author_avatar_height', 22));?>" />
					<?php endif;?>
					<?php echo JText::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section))?>
					<?php echo JText::sprintf('CONDATE', JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format')))?>
				</small></p>
				
				<?php if(!empty($item->fields_by_key[$gall_key]) && $item->fields_by_key[$gall_key]->result):?>
					<?php echo $item->fields_by_key[$gall_key]->result;?>
					<div class="clearfix"></div>
				<?php endif;?>
				
				<?php if(!empty($item->fields_by_key[$desc_key]) && $item->fields_by_key[$desc_key]->result):?>
					<div class="gall-description" style="margin-top: 5px;"><?php echo strip_tags($item->fields_by_key[$desc_key]->result);?></div>
				<?php endif;?>
				
				<div class="clearfix"></div>
			</div>
		</div>
	
	<?php if($k % $cols == ($cols - 1)):?>
		</div><hr>
	<?php endif; $k++;?>
	
<?php endforeach;?>

<?php if($k % $cols != 0):?>
	</div>
<?php endif;?>
</div>