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

?>
    

   
<div class="blog-list-recent">
    <?php foreach ($this->items AS $item): ?>
    <div class="item <?php if($item->featured) echo ' article-featured' ?>">
        <a href="<?php echo JRoute::_($item->url);?>"><?php echo $item->title?></a>
        <div class="date">
            <span><?php echo JText::_('On'); ?></span>
            <?php
              echo JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format'));
            ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>        
    

    
  
    


            

