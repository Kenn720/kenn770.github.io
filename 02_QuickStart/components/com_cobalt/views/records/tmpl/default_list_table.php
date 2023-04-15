<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$k = $p1 = 0;
$params = $this->tmpl_params['list'];
$total_fields_keys = $this->total_fields_keys;
$fh = new FieldHelper($this->fields_keys_by_id, $this->total_fields_keys);
JHtml::_('dropdown.init');
$pos1 = @$this->fields_keys_by_id[$params->get('tmpl_params.field_id_position_1')];
$pos2 = $params->get('tmpl_params.field_id_position_2');

$exclude = $params->get('tmpl_params.field_id_exclude');
ArrayHelper::clean_r($exclude);
foreach ($exclude as &$value) {
	$value = @$this->fields_keys_by_id[$value];
}

$excludeheader = $params->get('tmpl_params.field_id_exclude_header');
ArrayHelper::clean_r($excludeheader);
foreach ($excludeheader as &$value) {
	$value = @$this->fields_keys_by_id[$value];
}
$pos1width = $params->get('tmpl_params.first_width', '1%');
?>


<style>
	.user-info {
		margin: 0;
	}
	.avatar {
		text-align: center;
		vertical-align: middle;
	}
	.table-records tbody tr td {
		vertical-align: <?php echo $params->get('tmpl_core.valign') ?>;
	}
	.record-title {
		margin: 0 0 5px 0;
	}
.relative_ctrls {
	position: relative;
}
.user-ctrls {
	position: absolute;
	top:-24px;
	right: 0;
}
	</style>

<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo JText::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<table class="table-records section-<?php echo $this->section->id; ?> <?php echo implode(' ', (array)$params->get('tmpl_core.table_style')); ?>  cob-section-<?php echo $this->section->id; ?>">
	<?php if($params->get('tmpl_params.table_header', 1)):?>
		<thead>
			<tr>
 				<?php if($pos1 && $fh->isExists($pos1)): ?>
					<th width="1%" nowrap="nowrap" class="cob-p1">
						<?php if(!in_array($pos1, $excludeheader)):?>
							<?php if($fh->hasIcon($pos1)):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/<?php echo $fh->icon($pos1);?>" align="absmiddle" />
							<?php endif;?>
						<?php echo $fh->label($pos1);?>
						<?php endif;?>
					</th>
					<?php $p1 = true; if(isset($total_fields_keys[$pos1])) unset($total_fields_keys[$pos1]);?>
				<?php endif;?>

				<th class="cob-title-th">
					<?php if($params->get('tmpl_params.item_icon_title')): ?>
						<img src="<?php echo JURI::root();?>media/mint/icons/16/<?php echo $params->get('tmpl_params.item_icon_title_icon') ?>" align="absmiddle" />
					<?php endif;?>
					<?php echo JText::_($params->get('tmpl_params.lbl_title', 'CTITLE'));?>
				</th>

				<?php if($params->get('tmpl_core.item_rating') == 1):?>
					<th class="cob-rating-th">
						<?php if($params->get('tmpl_params.item_icon_rating')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/tick.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CRATING');?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_author_avatar') == 1):?>
					<th class="cob-avatar-th">
						<?php echo JText::_('CAVATAR');?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_author') == 1):?>
					<th nowrap="nowrap" class="cob-author-th">
						<?php if($params->get('tmpl_params.item_icon_author')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/user.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CAUTHOR');?>
					</th>
				<?php endif;?>


				<?php if($params->get('tmpl_core.item_type') == 1):?>
					<th class="cob-type-th">
						<?php if($params->get('tmpl_params.item_icon_type')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/block.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CTYPE')?>
					</th>
				<?php endif;?>


				<?php if($pos2):?>
					<?php foreach ($pos2 AS $id): $key = $this->fields_keys_by_id[$id];?>
						<?php if(isset($total_fields_keys[$key])) unset($total_fields_keys[$key]);?>
					<?php endforeach;?>
				<?php endif;?>

				<?php foreach ($total_fields_keys AS $field):?>
					<?php if(in_array($field->key, $exclude)) continue; ?>
					<th width="1%" nowrap="nowrap" class="cob-f<?php echo $field->id; ?>">
						<?php if(!in_array($field->key, $excludeheader)):?>
							<?php if($field->params->get('core.icon') && $params->get('tmpl_params.item_icon_fields')):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/<?php echo $field->params->get('core.icon');?>" align="absmiddle" />
							<?php endif;?>
							<?php echo JText::_($field->label);?>
						<?php endif;?>
					</th>
				<?php endforeach;?>

				<?php if($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')):?>
					<th nowrap="nowrap" class="cob-ucat-th">
						<?php if($params->get('tmpl_params.item_icon_user_categories')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/category.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CCATEGORY');?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_categories') == 1 && $this->section->categories):?>
					<th nowrap="nowrap" class="cob-cat-th">
						<?php if($params->get('tmpl_params.item_icon_categories')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/category.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_($params->get('tmpl_core.lbl_category', 'CCATEGORY'));?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_ctime') == 1):?>
					<th nowrap="nowrap" class="cob-ctime-th">
						<?php if($params->get('tmpl_params.item_icon_ctime')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/calendar-day.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CCREATED');?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_mtime') == 1):?>
					<th nowrap="nowrap" class="cob-mtime-th">
						<?php if($params->get('tmpl_params.item_icon_mtime')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/calendar-day.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CCHANGED');?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_extime') == 1):?>
					<th nowrap="nowrap" class="cob-extime-th">
						<?php if($params->get('tmpl_params.item_icon_extime')):?>
							<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/calendar-day.png" align="absmiddle" />
						<?php endif;?>
						<?php echo JText::_('CEXPIRE');?>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_comments_num') == 1):?>
					<th nowrap="nowrap" class="cob-comm-th">
						<span rel="tooltip" data-original-title="<?php echo JText::_('CCOMMENTS');?>">
							<?php if($params->get('tmpl_params.item_icon_comments_num')):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/balloon-left.png" align="absmiddle" />
							<?php else:?>
								<?php echo JString::substr(JText::_('CCOMMENTS'), 0, 1)?>
							<?php endif;?>
						</span>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_vote_num') == 1):?>
					<th nowrap="nowrap" class="cob-vote-th">
						<span rel="tooltip" data-original-title="<?php echo JText::_('CVOTES');?>">
							<?php if($params->get('tmpl_params.item_icon_vote_num')):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/star.png" align="absmiddle" />
							<?php else:?>
								<?php echo JString::substr(JText::_('CVOTES'), 0, 1)?>
							<?php endif;?>
						</span>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_favorite_num') == 1):?>
					<th nowrap="nowrap" class="cob-book-th">
						<span rel="tooltip" data-original-title="<?php echo JText::_('CFAVORITE');?>">
							<?php if($params->get('tmpl_params.item_icon_favorite_num')):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/bookmark.png" align="absmiddle" />
							<?php else:?>
								<?php echo JString::substr(JText::_('CFAVORITE'), 0, 1)?>
							<?php endif;?>
						</span>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_follow_num') == 1):?>
					<th nowrap="nowrap" class="cob-follow-th">
						<span rel="tooltip" data-original-title="<?php echo JText::_('CFOLLOWERS');?>">
							<?php if($params->get('tmpl_params.item_icon_follow_num')):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/follow1.png" align="absmiddle" />
							<?php else:?>
								<?php echo JString::substr(JText::_('CFOLLOWERS'), 0, 1)?>
							<?php endif;?>
						</span>
					</th>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_hits') == 1):?>
					<th nowrap="nowrap" width="1%" class="cob-hit-th">
						<span rel="tooltip" data-original-title="<?php echo JText::_('CHITS');?>">
							<?php if($params->get('tmpl_params.item_icon_hits')):?>
								<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/hand-point-090.png" align="absmiddle" />
							<?php else:?>
								<?php echo JString::substr(JText::_('CHITS'), 0, 1)?>
							<?php endif;?>
						</span>
					</th>
				<?php endif;?>
			</tr>
		</thead>
	<?php else:?>
		<?php if($pos1 && $fh->isExists($pos1)): ?>
			<?php $p1 = true; if(isset($total_fields_keys[$pos1])) unset($total_fields_keys[$pos1]);?>
		<?php endif;?>
		<?php if($pos2):?>
			<?php foreach ($pos2 AS $id): $key = $this->fields_keys_by_id[$id];?>
				<?php if(isset($total_fields_keys[$key])) unset($total_fields_keys[$key]);?>
			<?php endforeach;?>
		<?php endif;?>
	<?php endif;?>
	<tbody>
		<?php foreach ($this->items AS $item):?>
			<?php
			if((int)$params->get('tmpl_params.title_char')  && JString::strlen($item->title) > (int)$params->get('tmpl_params.title_char'))
			{
				$title = $item->title;
				$item->title = sprintf('<span rel="tooltip" data-original-title="%s">%s...</span>',
					htmlentities($title, ENT_COMPAT, 'utf-8'), JString::substr($title, 0, (int)$params->get('tmpl_params.title_char')));
			}
			?>
			<tr class="<?php
			if($item->featured)
			{
				echo ' success';
			}
			elseif($item->expired)
			{
				echo ' error';
			}
			elseif($item->future)
			{
				echo ' warning';
			}
			?>">
				<?php if($pos1 && $p1):?>
					<?php if(isset($item->fields_by_key[$pos1])):?>
						<?php $result = $item->fields_by_key[$pos1]->result;?>
						<td width="<?php echo $pos1width;?>" style="width: <?php echo $pos1width;?>" class="<?php echo $item->fields_by_key[$pos1]->params->get('core.field_class')?>"><?php echo $result; ?></td>
						<?php unset($item->fields_by_key[$pos1]);?>
					<?php else:?>
						<td width="<?php echo $pos1width;?>" style="width: <?php echo $pos1width;?>">&nbsp;</td>
					<?php endif;?>
				<?php endif;?>

				<td class="has-context cob-title-td">
					<div class="relative_ctrls">
						<?php if($this->user->get('id')):?>
							<div class="user-ctrls">
								<div class="btn-group" style="display: none;">
									<?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
									<?php echo HTMLFormatHelper::follow($item, $this->section);?>
									<?php echo HTMLFormatHelper::repost($item, $this->section);?>
									<?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
									<?php if($item->controls):?>
										<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-mini">
											<img width="16" height="16" alt="<?php echo JText::_('COPTIONS')?>" src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/gear.png">
										</a>
										<ul class="dropdown-menu">
											<?php echo list_controls($item->controls);?>
										</ul>
									<?php endif;?>
								</div>
							</div>
						<?php endif;?>

						<?php if($params->get('tmpl_core.item_title')):?>
							<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
								<div class="pull-left">
									<<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
										<?php if(in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())):?>
											<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
												<?php echo $item->title?>
											</a>
										<?php else :?>
											<?php echo $item->title?>
										<?php endif;?>

										<?php echo CEventsHelper::showNum('record', $item->id);?>
									</<?php echo $params->get('tmpl_core.title_tag', 'h2');?>>
								</div>
								<div class="clearfix"></div>
							<?php endif;?>
						<?php endif;?>

						<?php if($pos2): $ll = array();?>
							<dl class="text-overflow">
								<?php foreach ($pos2 AS $id): $id = $this->fields_keys_by_id[$id];?>
									<?php if(empty($item->fields_by_key[$id]->result)) continue;?>
									<?php $field = $item->fields_by_key[$id];?>
									<?php if($field->params->get('core.show_lable') > 1 && $params->get('tmpl_params.field_labels') == 1):?>
										<dt id="<?php echo $field->id;?>-lbl" for="field_<?php echo $field->id;?>" class="<?php echo $field->class;?>" >
											<?php echo $field->label; ?>
											<?php if($field->params->get('core.icon')):?>
												<img alt="<?php strip_tags($field->label)?>" src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/<?php echo $field->params->get('core.icon');?>" align="absmiddle">
											<?php endif;?>
										</dt>
									<?php endif;?>
									<dd class="input-field<?php echo ($field->params->get('core.label_break') > 1 ? '-full' : NULL)?>">
										<?php echo $field->result; ?>
									</dd>
								<?php endforeach;?>
							</dl>
						<?php endif;?>

						<?php if($params->get('tmpl_core.item_rating') == 2):?>
							<?php echo $item->rating;?>
						<?php endif;?>

						<?php
						$category = array();
						$author = array();
						$details = array();

						if($params->get('tmpl_core.item_categories') == 2 && $item->categories_links)
						{
							$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', JText::_($params->get('tmpl_core.lbl_category', 'CCATEGORY')), implode(', ', $item->categories_links));
						}
						if($params->get('tmpl_core.item_user_categories') == 2 && $item->ucatid)
						{
							$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', JText::_('CUSERCAT'), $item->ucatname_link);
						}
						if($params->get('tmpl_core.item_author') == 2 && $item->user_id)
						{
							$author[] = JText::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section));
							if($params->get('tmpl_core.item_author_filter'))
							{
								$author[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
							}
						}
						if($params->get('tmpl_core.item_ctime') == 2)
						{
							$author[] = JText::sprintf('CONDATE', JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
						}

						if($params->get('tmpl_core.item_mtime') == 2)
						{
							$author[] = sprintf('%s: %s', JText::_('CCHANGEON'), JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format')));
						}

						if($params->get('tmpl_core.item_extime') == 2)
						{
							$author[] = sprintf('%s: %s', JText::_('CEXPIREON'), $item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));
						}

						if($params->get('tmpl_core.item_type') == 2)
						{
							$details[] = sprintf('%s: %s %s', JText::_('CTYPE'), $item->type_name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('Show all records of type %s', $item->type_name), $this->section) : NULL));
						}
						if($params->get('tmpl_core.item_hits') == 2)
						{
							$details[] = sprintf('%s: %s', JText::_('CHITS'), $item->hits);
						}
						if($params->get('tmpl_core.item_comments_num') == 2)
						{
							$details[] = sprintf('%s: %s', JText::_('CCOMMENTS'), CommentHelper::numComments($this->submission_types[$item->type_id], $item));
						}
						if($params->get('tmpl_core.item_vote_num') == 2)
						{
							$details[] = sprintf('%s: %s', JText::_('CVOTES'), $item->votes);
						}
						if($params->get('tmpl_core.item_favorite_num') == 2)
						{
							$details[] = sprintf('%s: %s', JText::_('CFAVORITED'), $item->favorite_num);
						}
						if($params->get('tmpl_core.item_follow_num') == 2)
						{
							$details[] = sprintf('%s: %s', JText::_('CFOLLOWERS'), $item->subscriptions_num);
						}
						?>

						<?php if($category || $author || $details): ?>
							<div class="clearfix"></div>

							<div class="container-fluid well">
								<div class="row-fluid">
									<div class="span2 avatar">
										<?php if($params->get('tmpl_core.item_author_avatar') == 2):?>
											<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
										<?php endif;?>
									</div>
									<div class="span10">
										<small>
											<dl class="dl-horizontal user-info">
												<?php if($category):?>
													<?php echo implode(' ', $category);?>
												<?php endif;?>
												<?php if($author):?>
													<dt><?php echo JText::_('Posted');?></dt>
													<dd>
														<?php echo implode(', ', $author);?>
													</dd>
												<?php endif;?>
												<?php if($details):?>
													<dt>Info</dt>
													<dd class="hits">
														<?php echo implode(', ', $details);?>
													</dd>
												<?php endif;?>
											</dl>
										</small>
									</div>
								</div>
							</div>
						<?php endif;?>
					</div>
				</td>

				<?php if($params->get('tmpl_core.item_rating') == 1):?>
					<td nowrap="nowrap" class="cob-rating-td"><?php echo $item->rating;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_author_avatar') == 1):?>
					<td class="cob-avatar-td">
						<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
					</td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_author') == 1):?>
					<td nowrap="nowrap"  class="cob-avatar-td"><?php echo CCommunityHelper::getName($item->user_id, $this->section);?>
					<?php if($params->get('tmpl_core.item_author_filter') /* && $item->user_id */):?>
						<?php echo FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);?>
					<?php endif;?>
					</td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_type') == 1):?>
					<td nowrap="nowrap" class="cob-type-td"><?php echo $item->type_name;?>
					<?php if($params->get('tmpl_core.item_type_filter')):?>
						<?php echo FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $item->type_name), $this->section);?>
					<?php endif;?>
					</td>
				<?php endif;?>

				<?php foreach ($total_fields_keys AS $field):?>
					<?php if(in_array($field->key, $exclude)) continue; ?>
					<td class="<?php echo $field->params->get('core.field_class')?>" <?php if($params->get('tmpl_core.digit_right') && $field->type == 'digits') echo ' align="right"'?>><?php if(isset($item->fields_by_key[$field->key]->result)) echo $item->fields_by_key[$field->key]->result ;?></td>
				<?php endforeach;?>

				<?php if($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')):?>
					<td class="cob-ucat-td"><?php echo $item->ucatname;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_categories') == 1 && $this->section->categories):?>
					<td class="cob-cat-th"><?php echo implode(', ', $item->categories_links);?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_ctime') == 1):?>
					<td class="cob-ctime-td"><?php echo JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format'));?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_mtime') == 1):?>
					<td class="cob-mtime-td"><?php echo JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_extime') == 1):?>
					<td class="cob-extime-td"><?php echo ( $item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_comments_num') == 1):?>
					<td class="cob-comm-td"><?php echo CommentHelper::numComments($this->submission_types[$item->type_id], $item);?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_vote_num') == 1):?>
					<td class="cob-vote-td"><?php echo $item->votes;?></td>
				<?php endif;?>


				<?php if($params->get('tmpl_core.item_favorite_num') == 1):?>
					<td class="cob-book-td"><?php echo $item->favorite_num;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_follow_num') == 1):?>
					<td class="cob-follow-td"><?php echo $item->subscriptions_num;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_hits') == 1):?>
					<td class="cob-hit-td"><?php echo $item->hits;?></td>
				<?php endif;?>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>