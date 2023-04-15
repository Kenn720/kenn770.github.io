<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$value = number_format($this->value, $this->params->get('params.decimals_num'), $this->params->get('params.dseparator', ''), $this->params->get('params.separator', ''));
?>

<?php echo $this->params->get('params.prepend');?>

<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8');?>

<?php echo $this->params->get('params.append');?>