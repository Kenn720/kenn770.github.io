<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
 defined('_JEXEC') or die('Restricted access');
?>
<div style="clear: both;"></div>
<style>
<!--
.mod_sstat_table tr, .mod_sstat_table  td {
	border: 1px solid #DDDDDD;
    padding: 10px;
}
-->
</style>
<table class="mod_sstat_table">
	<?php foreach ($data as $title => $value):?>
	<tr><td><?php echo JText::_(strtoupper($params->get($title)));?></td><td><?php echo $value;?></td></tr>
	<?php endforeach;?>
</table>
<div style="clear: both;"></div>