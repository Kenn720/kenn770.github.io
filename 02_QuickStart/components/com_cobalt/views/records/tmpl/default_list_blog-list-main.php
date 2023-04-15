<?php
/**
 * by Torbara
 * a component for Joomla! 1.7 + CMS (http://www.joomla.org)
 * Author: Vadim Kozhukhov
 * Author Website: http://www.torbara.com/
 * @copyright Copyright (C) 2015 Torbara (http://www.torbara.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('jquery.framework');
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
define('JPATH_COMPONENT', JPATH_SITE . '/components/com_cobalt');
$params = $this->tmpl_params['list'];
$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
if (file_exists($commentsAPI)) {
    require_once($commentsAPI);
}
?>
    

   

    <?php foreach ($this->items AS $item): ?>
    <div class="uk-width-medium-1-3">
        <div class="blog-list-item main <?php if($item->featured) echo ' article-featured' ?>">
            <div class="main-date">
                <div class="day"><?php echo JHtml::_('date', $item->created, 'd'); ?></div>
                <div class="month"><?php echo JHtml::_('date', $item->created, 'M.'); ?></div>
            </div>
            <div class="blog-list-image main">
                <?php if(@$item->fields_by_id[9]): ?>
                    <?php $field = $item->fields_by_id[9]; // Artcile images ?>
                    <?php echo $field->result; ?>
                <?php endif; ?>
            </div>
            <div class="article-info">

                <a href="<?php echo JRoute::_($item->url);?>"><h3><?php echo $item->title?></h3></a>


                <div class="intro-text">
                    <?php $field = $item->fields_by_id[10]; // Artcile Text ?>
                    <?php echo $field->result; ?>
                </div>
            </div>

        </div>
    </div> 
    <?php endforeach; ?>

      
    

    
  
    


            

