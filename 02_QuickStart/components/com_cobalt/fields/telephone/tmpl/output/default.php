<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$width = $this->params->get('params.qr_width', 120);
?>

<?php echo $this->phone;?>

<?php if($this->params->get('params.qr_code', 0)):?>
	<?php $img = '<img src="http://chart.apis.google.com/chart?chs='.$width.'x'.$width.'&cht=qr&chld=L|0&chl=TEL:'.$this->qrvalue.'" width="'.$width.'" height="'.$width.'" align="absmiddle">'; ?>
	<img style="cursor: pointer" src="<?php echo JURI::root(true) ?>/media/mint/icons/16/barcode-2d.png" rel="popover" data-content="<?php echo htmlentities($img, ENT_QUOTES);?>" data-original-title="<?php echo JText::_('T_QR');?>">
<?php endif; ?>

		