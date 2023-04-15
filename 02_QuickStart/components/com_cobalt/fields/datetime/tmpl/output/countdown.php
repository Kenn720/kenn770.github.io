<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JFactory::getDocument()->addScript(JURI::root(TRUE) . '/media/mint/js/moocountdown/SimpleCounter.js');

$val = $this->value;
rsort($val);
$val = $val[0];

$date = '<span class="countdown_date">'.$this->_getFormatted($val, true).'</span>';
$text = (strtotime($val) > time()) ? JText::sprintf('D_LEFT', $date) : JText::sprintf('D_PAST', $date);

$format = $this->params->get('params.time', 0) ?  '{D} {H} {M} {S}' : '{D}';
$format = '{D} {H} {M} {S}';

$diff = time() - strtotime($val);
$days = $diff / 3600 / 24;
$color = '';
$type = 'normal';
if ($days < 0)
{
	if (abs($days) <= $this->params->get('params.notify_days', 30))
	{
		$type = 'notify';
	}
}
else
{
	$type = 'past';
}
if ($this->params->get('params.' . $type . '_color') != '')
	$color = 'style=\'color: ' . $this->params->get('params.' . $type . '_color') . '\'';
?>

<style>
	.countdown_msg { font-size: 16px; font-weight: bold; }
	.countdown_date { color: #bbb; }
	.countdown_number { font-size: 26px; font-weight: bold; }
</style>

<p class="countdown_msg"><?php echo $text;?>: </p>

<div id="countdown<?php echo $record->id;?>"></div>

<div class="clearfix"></div>

<script type='text/javascript'>
	new SimpleCounter("countdown<?php echo $record->id;?>", <?php echo strtotime($val);?> ,{
		'continue':0,
		format: '<?php echo $format?>',
		lang : {
			d:{single:'<?php echo JText::_('D_COUNTDAY');?>',plural:'<?php echo JText::_('D_COUNTDAYS');?>'},       //days
			h:{single:'<?php echo JText::_('D_COUNTHOUR');?>',plural:'<?php echo JText::_('D_COUNTHOURS');?>'},     //hours
			m:{single:'<?php echo JText::_('D_COUNTMINUTE');?>',plural:'<?php echo JText::_('D_COUNTMINUTES');?>'}, //minutes
			s:{single:'<?php echo JText::_('D_COUNTSECOND');?>',plural:'<?php echo JText::_('D_COUNTSECONDS');?>'}  //seconds
		},
		formats : {
			full : "<span class='countdown_number' <?php echo $color;?>>{number}</span> <span class='countdown_word' <?php echo $color;?>>{word}</span>", //Format for full units representation
			shrt : "<span class='countdown_number' <?php echo $color;?>>{number}</span>" //Format for short unit representation
		}
	});
</script>
