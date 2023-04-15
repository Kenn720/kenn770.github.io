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

$print = '';
if($this->params->get('params.format') == 'custom')
{
	$d = strpos($this->params->get('params.custom'), '%d');
	if($d === false)
	{
		if(($d = strpos($this->params->get('params.custom'), '%e')) === false)
			$d = -1;
	}
	$m = strpos($this->params->get('params.custom'), '%m');
	if($m === false)
	{
		if(($m = strpos($this->params->get('params.custom'), '%b')) === false)
		{
			if(($m = strpos($this->params->get('params.custom'), '%B')) === false)
			{
				if(($m = strpos($this->params->get('params.custom'), '%h')) === false)
					$m = -1;
			}
		}
	}

	if($m != -1 || $d != -1)
	{
		if($d < $m) $print = 'day';
		else $print = 'month';
	}
}
$range = explode(',', @$this->value['range']);
$value = '';
if (@$range[0] || @$range[1])
{
	$value = $this->_getFormatted(@$range[0], true).' - '.$this->_getFormatted(@$range[1], true);
}
?>

<input type="text" class="cdate-field" value="<?php echo $value;?>" id="field_<?php echo $module.$this->id;?>" size="30" data-field-options="{type: '<?php echo $print;?>', name: 'filters[<?php echo $this->key;?>][range]', input: 'filter'}"  />
<input type="hidden" value="<?php echo $this->value['range'];?>" id="filter_<?php echo $module.$this->id;?>" name="filters[<?php echo $this->key;?>][range]" />
<script type="text/javascript">
	picker<?php echo $module.$this->id;?> = new Picker.Date.Range(document.getElementById('field_<?php echo $module.$this->id;?>'), {
		timePicker:  0,
		columns: <?php echo $this->params->get('params.columns', 2);?>,
		positionOffset: {x: 5, y: 0},
		format: '<?php echo $this->php_format; ?>',
		onSelect: function (dates, input)
		{
			var hidden_s = dates.format('<?php echo $format_js;?>');
			var hidden_f = input.format('<?php echo $format_js;?>');
			jQuery('#filter_<?php echo $module.$this->id;?>').val(hidden_s + ',' + hidden_f);
			var formatted_s = dates.format_php('<?php echo $this->php_format; ?>');
			var formatted_f = input.format_php('<?php echo $this->php_format; ?>');
			jQuery('#field_<?php echo $module.$this->id;?>').val(formatted_s + ' - ' + formatted_f);
		}
	});
</script>