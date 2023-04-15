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
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
	<input align="right" type="button" class="btn btn-primary" onclick="javascript:submitbutton('tools.apply')" value="<?php echo JText::_('CRUNTOOL')?>" class="button" style="float: right;" />

	<br style="clear: both;" />	
	<br />	
	<?php echo $this->form; ?>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form> 