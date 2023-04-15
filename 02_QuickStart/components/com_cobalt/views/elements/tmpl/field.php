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
<?php if(JFactory::getApplication()->input->getInt('width')):?>
<style>
<!--
body, body div {
	max-width:<?php echo JFactory::getApplication()->input->getInt('width');?>px !important;
    overflow-y: auto !important;
}
-->
</style>
<?php endif;?>

<?php 
echo $this->context;
?> 