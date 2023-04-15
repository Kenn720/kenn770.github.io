<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$format_js = '%Y-%m-%d';
if ($this->params->get('params.time', 0))
	$format_js .= ' %H:%M';
$value = @$this->value;
?>

<input type="text" class="cdate-field" value="<?php echo $this->_getFormatted($value, true);?>" id="field_<?php echo $module.$this->id;?>" size="30"/>
<input type="hidden" value="<?php echo $value;?>" id="filter_<?php echo $module.$this->id;?>" name="filters[<?php echo $this->key;?>]">

<script type="text/javascript">
	picker<?php echo $this->id;?> = new Picker.Date(document.getElementById('field_<?php echo $module.$this->id;?>'), {
		timePicker:  <?php echo $this->params->get('params.time', 0);?>,
		columns: <?php echo $this->params->get('params.columns', 2);?>,
		positionOffset: {x: 5, y: 0},
		format: '<?php echo $this->format;?>',
		onSelect: function (date)
		{
			var hidden = date.format('<?php echo $format_js;?>');
			var formatted = date.format_php('<?php echo $this->php_format; ?>');
			jQuery('#filter_<?php echo $module.$this->id;?>').val(hidden);
			jQuery('#field_<?php echo $module.$this->id;?>').val(formatted);
		}
	});
</script>
