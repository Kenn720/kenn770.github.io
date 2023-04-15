<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<?php global $app, $option, $Itemid;?>
<?php if (JFactory::getApplication()->input->getInt('modal', 0)):?>
<script type="text/javascript">
	window.parent.SqueezeBox.close();
</script>
<?php endif;?>


<?php echo $this->loadTemplate('markup_'.$this->section->params->get('general.tmpl_markup'));?>
