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

?>


<article class="portfolio-single <?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
    
    <?php if($this->user->get('id')&&($item->controls)):?>			
        <ul class="admin-edit">
            <?php echo list_controls($item->controls);?>
        </ul>
    <?php endif;?>
    <div class="clearfix"></div>
    <div class="uk-grid">
        <div class="uk-width-1-1">
            <h2 class="portfolio-title"><?php echo $item->title?></h2>
    
            <?php if(@$item->fields_by_id[1]): ?>
                <p>
                    <?php $field = $item->fields_by_id[1]; // Top Description ?>
                    <?php echo $field->result; ?>  
                </p>
            <?php endif; ?>
        </div>
        <div class="uk-width-1-1">
            <div class="slider-article">
                <div class="uk-slidenav-position" data-uk-slider="{autoplay:true,autoplayInterval:4000}">
                    <div class="uk-slider-container ">
                    <ul class="uk-slider uk-grid-width-medium-1-1">  
                        <?php $field = $item->fields_by_id[2]; // Top Image

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
                    </div>
                    <a href="" class="uk-slidenav uk-slidenav-previous" data-uk-slider-item="previous"></a>
                    <a href="" class="uk-slidenav uk-slidenav-next" data-uk-slider-item="next"></a>
                </div>
            </div>
            
        </div>
        
        <div class="uk-width-medium-8-10 uk-push-1-10">
            <div class="uk-grid">
                <?php if(@$item->fields_by_id[3]): ?>
                <div class="uk-width-medium-6-10">
                    <div class="description">
                        <?php $field = $item->fields_by_id[3]; // Project Description ?>
                        <div class="description-title"><?php echo $field->label; ?></div>
                        <?php echo $field->result; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="uk-width-medium-4-10">
                <div class="description-title"><?php echo $item->fields_by_id[4]->group_title; ?></div>
                <ul class="details">
                    <li>
                        <?php $field = $item->fields_by_id[4]; // Project Details ?>
                        <span><?php echo $field->label; ?></span>
                        <?php echo $field->result; ?>
                    </li> 
                    
                    <li>
                        <?php $field = $item->fields_by_id[5]; // Project Details ?>
                        <span><?php echo $field->label; ?></span>                            
                        <?php $total = count($item->categories);$counter = 0;foreach ($item->categories as $cat){$counter++;if ($counter == $total){{ echo $cat;}  } else {{ echo $cat.', '; }}} ?> 
                    </li> 
                    
                    <?php if(@$item->fields_by_id[6]): ?>
                        <li>
                            <?php $field = $item->fields_by_id[6]; // Project Details ?>
                            <span><?php echo $field->label; ?></span>
                            <?php echo $field->result; ?>
                        </li> 
                    <?php endif; ?>
                    <li>
                        <span>Likes</span> {jlikedislike portfolio_<?php echo $item->id; ?>}
                    </li> 
                    <?php if(@$item->fields_by_id[8]): ?>
                        <li>
                            <?php $field = $item->fields_by_id[8]; // Project Details ?>
                            <span><?php echo $field->label; ?></span>
                            <?php echo $field->result; ?>
                        </li> 
                    <?php endif; ?>
                </ul>
            </div>
            </div>        
        </div>
    </div>
    
        
</article>


