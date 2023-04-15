<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<style>
.unstyled li.mod-avatar {
	margin-right: 5px;
	margin-bottom: 5px;
}
</style>
<ul class="unstyled">
	<?php foreach ($list as $id):?>
	  <li class="mod-avatar pull-left">
		  <?php 
		  $lbl = '<img src="'.CCommunityHelper::getAvatar($id, $params->get('ava_size', 32), $params->get('ava_size', 32)).'" class="'.$params->get('ava_style', 'img-polaroid').'">';
		  $options = array('nobadge' => 1, 'label' => $lbl, 'noonlinestatus' => 1, 'tooltip' => 'name');
		  ?>
	  	  <?php echo CCommunityHelper::getName($id, $section, $options);?>
	  </li>
	<?php endforeach;?>
</ul>
<div class="clearfix"></div>

