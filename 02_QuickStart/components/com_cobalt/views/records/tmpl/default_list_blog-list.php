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
    

   
<div class="blog-list-wrap">
    <?php foreach ($this->items AS $item): ?>
    <div class="blog-list-item <?php if($item->featured) echo ' article-featured' ?>">
        <div class="main-date">
            <div class="day"><?php echo JHtml::_('date', $item->created, 'd'); ?></div>
            <div class="month"><?php echo JHtml::_('date', $item->created, 'M.'); ?></div>
        </div>
        <div class="blog-list-image">
            <?php if(@$item->fields_by_id[9]): ?>
                <?php $field = $item->fields_by_id[9]; // Artcile images ?>
                <?php echo $field->result; ?>
            <?php endif; ?>
        </div>
        <div class="article-info">
            
            <a href="<?php echo JRoute::_($item->url);?>"><h3><?php echo $item->title?></h3></a>
        
            <div class="article-param">
                <div class="author">
                    <span><?php echo JText::_('By'); ?></span>
                    <?php
                    $autor = FilterHelper::filterLink('filter_user', $item->user_id, CCommunityHelper::getName($item->user_id, $this->section), JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
                    echo str_replace("]", "%5D", str_replace("[", "%5B", $autor));
                    ?>
                </div>
                <div class="categories">
                    <span><?php echo JText::_('In'); ?></span>
                    <?php echo implode(', ', $item->categories_links); ?>
                </div>

                <div class="date">
                    <span><?php echo JText::_('On'); ?></span>
                    <?php
                      echo JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format'));
                    ?>
                </div>
                <div class="count"><?php echo JText::_('Comments:'); ?> <?php echo JComments::getCommentsCount($item->id, 'com_cobalt'); ?></div>

            </div>
            <div class="intro-text">
                <?php $field = $item->fields_by_id[10]; // Artcile Text ?>
                <?php echo $field->result; ?>
            </div>
        </div>

    </div>
    <?php endforeach; ?>
</div>        
    

    
  
    


            

