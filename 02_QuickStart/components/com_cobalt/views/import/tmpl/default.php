<?php
/**
 * by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<div class="page-header">
	<h1><?php echo JText::_('CIMPORT')?></h1>
</div>

<?php 
echo $this->loadTemplate('step'.JFactory::getApplication()->input->get('step', 1));
?>
