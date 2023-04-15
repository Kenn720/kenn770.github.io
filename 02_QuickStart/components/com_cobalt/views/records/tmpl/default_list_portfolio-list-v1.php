<?php
/**
 * by Torbara
 * a component for Joomla! 1.7 + CMS (http://www.joomla.org)
 * Author: Vadim Kozhukhov
 * Author Website: http://www.torbara.com/
 * @copyright Copyright (C) 2015 Torbara (http://www.tobara.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('jquery.framework');
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
define('JPATH_COMPONENT', JPATH_SITE . '/components/com_cobalt');
$params = $this->tmpl_params['list'];
$itemgutter = $params->get('tmpl_core.item_grid_size');
$itemcolumns = $params->get('tmpl_core.item_list_columns', 3);
?>
    

    <div class="button-group filter-button-group">
        <div class="uk-container uk-container-center" id="control-btn">
            <button class="active" data-uk-filter="">all</button><?php
            $arr = array();
            foreach ($this->items AS $item){ 
                foreach ($item->categories as $cat) { $arr [] = $cat; }
            }
            $arr = array_unique($arr);

            foreach ($arr as $category) {
                ?><button data-uk-filter="<?php echo "tt_".md5($category); ?>"><?php echo $category; ?></button><?php
            }?>
        </div>    
    </div>
        
    <div class="uk-grid uk-grid-collapse grid portfolio-list" data-uk-grid="{controls: '#control-btn',duration:400, gutter:<?php echo $itemgutter; ?>}">
        <?php $count = 0; ?>  
        <?php foreach ($this->items AS $item): ?>


            <div class="uk-width-medium-1-<?php echo $itemcolumns; ?> grid-item" data-uk-filter='<?php $total = count($item); $counter = 0; foreach($item->categories as $cat){ $counter++; if($counter == $total){ { echo "tt_".md5($cat).","; } } else{ { echo "tt_".md5($cat).""; } } }?>' >
                <div class="wrap">
                    <div class="img-wrap">
                        <?php 
                            $field = $item->fields_by_id[2]; // image
                            echo $field->result;
                        ?>
                    </div>
                    <div class="info">
                        <a href="<?php echo JRoute::_($item->url);?>" class="name">
                            <?php echo $item->title?>
                        </a>
                        <div class="category"><?php $total = count($item->categories);$counter = 0;foreach ($item->categories as $cat){$counter++;if ($counter == $total){{ echo $cat;}  } else {{ echo $cat.', '; }}} ?> </div>
                        {jlikedislike portfolio_<?php echo $item->id; ?>}
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        <?php $count++;?>
        <?php endforeach; ?>

    </div>

    
<!--<div class="uk-width-1-1 uk-text-center more-wrap">
    <a href="#" class="btn large brown"></a>
</div>-->
    

    
  
    


            

