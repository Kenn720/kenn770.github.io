<?php
/**
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.torbara.com/
 * @copyright Copyright (C) 2015 Torbara (http://www.torbara.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<?php if(in_array($this->type->params->get('properties.item_can_view_tag', 1), $this->user->getAuthorisedViewLevels())) :

	$app         = JFactory::getApplication();
	$attach_only = TRUE;
	if(MECAccess::allowAccessAuthor($this->type, 'properties.item_can_add_tag', $this->item->user_id) || MECAccess::allowUserModerate($this->user, $this->section, 'allow_tags'))
	{
		$attach_only = FALSE;
	}
	?>


	<?php
	if(
		MECAccess::allowAccessAuthor($this->type, 'properties.item_can_add_tag', $this->item->user_id) ||
		MECAccess::allowAccessAuthor($this->type, 'properties.item_can_attach_tag', $this->item->user_id) ||
		MECAccess::allowUserModerate($this->user, $this->section, 'allow_tags')
	):
		?>
		<dl class="dl-horizontal">
			<dt id="tags-dt">
				<?php echo JText::_('CTAGS'); ?> <?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
			</dt>
			<dd id="tags-dd">
				<div id="add-tags-block<?php echo $this->item->id; ?>">
					<?php echo JHtml::_('tags.add_button', $this->item->id, $this->type->params->get('properties.item_tags_max', 25), $attach_only); ?>
				</div>
			</dd>
		</dl>

	<?php else: ?>
		<?php if($this->item->tags): ?>
			<?php
			if(count($this->item->categories) > 0 && $this->section->params->get('general.filter_mode') == 0) {
				$keys = array_keys($this->item->categories);
				$catid = array_shift($keys);
			}
			$tags = JHtml::_('tags.fetch2',
				$this->item->tags,
				$this->item->id,
				$this->section->id,
				$app->input->getInt('cat_id', @$catid),
				$this->type->params->get('properties.item_tag_htmltags', 'h1, h2, h3, h4, h5, h6, strong, em, b, i, big'),
				$this->type->params->get('properties.item_tag_relevance', 0),
				$this->type->params->get('properties.item_tag_num', 0),
				$this->type->params->get('properties.item_tags_max', 25)
			);
			?>

                        <div class="tags-wrap">
                            <div id="tag-list-<?php echo $this->item->id ?>" class="tag_list">
                                    <i class="uk-icon-tags"></i>
                                    <span class="tag-title"><?php echo JText::_('Tags:'); ?></span>
                                    <?php 
                                    $tcout = count($tags);
                                    $count = 0;
                                    ?>
                                    <?php foreach($tags AS $tag): $count++; ?>
                                            <span class="tag_list_item">
                                                <?php
                                                    $tag_link = $tag['link'];
                                                    $tag_link = str_replace("[", "%5B", $tag_link);
                                                    $tag_link = str_replace("]", "%5D", $tag_link);
                                                ?>
                                                <a href="<?php echo $tag_link; ?>" <?php echo $tag['attr'] ?>><?php echo $tag['tag'] ?></a>
                                                <?php if($count != $tcout ) { echo ","; } ?>
                                            </span>
                                    <?php endforeach; ?>
                            </div>
                        </div>
			<div class="clearfix"></div>
			<br>
		<?php endif; ?>
	<?php  endif; ?>
<?php endif; ?>

