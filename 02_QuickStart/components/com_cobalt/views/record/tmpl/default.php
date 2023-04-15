<?php 
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<div class="contentpaneopen">
	<?php echo $this->loadTemplate('record_'.$this->type->params->get('properties.tmpl_article', 'default'));?>
	
	<div id="comments"><?php echo $this->loadTemplate('comments');?></div>
</div>