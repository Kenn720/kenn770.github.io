<?php
/**
 * a component for Joomla! 1.7 + (http://www.joomla.org)
 * Author: Vadim Kozhukhov
 * Author Website: http://www.torbara.com/
 * @copyright Copyright (C) 2015 Torbara (http://www.torbara.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$item = $this->item;
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$started = FALSE;
$i = $o = 0;

$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
if (file_exists($commentsAPI)) {
    require_once($commentsAPI);
}

?>


<article class="blog-single <?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
    
    <?php if($this->user->get('id')&&($item->controls)):?>			
        <ul class="admin-edit">
            <?php echo list_controls($item->controls);?>
        </ul>
    <?php endif;?>
    <div class="clearfix"></div>
    <div class="uk-grid">
        <div class="uk-width-1-1">
            <div class="slider-article">
                <div class="uk-slidenav-position" data-uk-slider="{autoplay:true,autoplayInterval:4000}">
                    <div class="uk-slider-container">
                        <div class="uk-slidenav-position">
                            <ul class="uk-slider uk-grid-width-1-1"> 
                                <?php $field = $item->fields_by_id[9]; // Article Slider

                                $key = $field->id . '-' . $record->id;
                                $dir = JComponentHelper::getParams('com_cobalt')->get('general_upload') . DIRECTORY_SEPARATOR . $field->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR; ?>

                                <?php
                                    foreach($field->value as $picture_index => $file) {
                                        $url =  CImgHelper::getThumb($dir . $file['fullpath'], $field->params->get('params.full_width', 100), $field->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
                                            array(
                                                'mode'       => $field->params->get('params.full_mode', 6),
                                                'strache'    => $field->params->get('params.full_stretch', 1),
                                                'background' => $field->params->get('params.thumbs_background_color', "#000000"),
                                                'quality'    => $field->params->get('params.full_quality', 80)
                                            ));  ?>
                                <li>    
                                    <img src="<?php echo $url; ?>" alt=""><?php
                                 } ?>
                                </li>
                            </ul>
                            <a href="" class="uk-slidenav uk-slidenav-previous" data-uk-slider-item="previous"></a>
                            <a href="" class="uk-slidenav uk-slidenav-next" data-uk-slider-item="next"></a>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="article-param">
                <div class="categories">
                    <i class="uk-icon-list-ul"></i>
                    <span><?php echo JText::_('In Category'); ?></span>
                    <?php echo implode(', ', $item->categories_links); ?>
                </div>
                <div class="author">
                    <span><?php echo JText::_('Posted by'); ?></span>
                    <?php
                    $autor = FilterHelper::filterLink('filter_user', $item->user_id, CCommunityHelper::getName($item->user_id, $this->section), JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
                    echo str_replace("]", "%5D", str_replace("[", "%5B", $autor));
                    ?>
                </div>
                <div class="date">
                    <span><?php echo JText::_('Date'); ?></span>
                    <?php
                      echo JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format'));
                    ?>
                </div>
                <div class="count"><i class="uk-icon-comments"></i><span><?php echo JComments::getCommentsCount($item->id, 'com_cobalt'); ?></span></div>
                
            </div>
            <h2 class="portfolio-title"><?php echo $item->title?></h2>
    
            <?php if(@$item->fields_by_id[10]): ?>
                
                    <?php $field = $item->fields_by_id[10]; // Artcile Text ?>
                    <?php echo $field->result; ?>  
                
            <?php endif; ?>
        </div>
        
        
        
    </div>
    
  <?php echo $this->loadTemplate('waxomtags');?>      
</article>


