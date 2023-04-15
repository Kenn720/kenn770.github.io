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
<?php if ($this->params->get('params.total_limit')):?>
	<small><?php echo JText::sprintf('F_OPTIONSLIMIT', $this->params->get('params.total_limit'));?></small>
	<br>
<?php endif; ?>


<?php echo $this->inputvalue;?>
