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

<div id="shorttext<?php echo $this->id?>">
	<?php echo $this->value_striped?>
</div>
<div id="hiddentext<?php echo $this->id?>" style="display: none;">
	<?php echo $this->value?>
</div>
<a href="javascript:void(0);"
	onclick="jQuery( '#shorttext<?php echo $this->id?>, #hiddentext<?php echo $this->id?>' ).slideToggle();"><?php echo JText::_($this->params->get('params.readmore_lbl','H_READMORE'));?></a>
