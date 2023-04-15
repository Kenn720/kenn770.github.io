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
<?php
$format_js = '%Y-%m-%d';
$print = 'month';
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

if ($this->params->get('params.time', 0))
	$format_js .= ' %H:%M';
?>
<style type="text/css">
	.default_dates .alert {
		float: left;
		margin-right: 10px;
		margin-bottom: 10px;
	}
</style>
<div class="default_dates" id="field_container<?php echo $this->id;?>">
	<?php if ($this->value) : ?>
	<?php
		$hidden = array();
		$dates = array();
		foreach($this->value as $k => $val) :
			$date = new CDate($val);
			$id = $date->format('%Y%m%d');
			$date = $this->_getFormatted($val, true);
			if ($k > 1)
				break;
			$dates[] = $date;
			$hidden[] = '<input type="hidden" value="' . $val . '" name="jform[fields][' . $this->id . '][]">';
		endforeach;
		?>
			<div id="date<?php echo $this->id;?>" class="alert alert-info">
				<a class="close" data-dismiss="alert" href="#">x</a>
				<?php echo implode(' - ', $dates);?>
				<?php echo implode('', $hidden);?>
			</div>
	<?php endif; ?>
</div>
<div class="clearfix"></div>

<input type="hidden" value="<?php echo (isset($this->value[0]) ? $this->value[0] : '');?>" data-field-options="{type: '<?php echo $print;?>', name: 'jform[fields][<?php echo $this->id;?>]'}" id="field_<?php echo $this->id;?>">

<a href="javascript: void(0);" class="btn btn-warning" id="date_toggle<?php echo $this->id;?>">
	<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/calendar-select-days-span.png" align="absmiddle" title="<?php echo JText::_('D_SELECTDATE');?>">
	<?php echo JText::_('D_CALENDAR');?>
</a>

<script type="text/javascript">
picker<?php echo $this->id;?> = new Picker.Date.Range($('field_<?php echo $this->id;?>'), {
	toggle: $('date_toggle<?php echo $this->id;?>'),
	columns: <?php echo $this->params->get('params.columns', 2);?>,
	positionOffset: {x: 5, y: 0},
	format: '<?php echo $this->format;?>',
	onSelect: function (dates, input)
	{
		var formatted_s = dates.format_php(this.options.format);
		var hidden_s = dates.format('<?php echo $format_js;?>');
		var formatted_f = input.format_php(this.options.format);
		var hidden_f = input.format('<?php echo $format_js;?>');
		id = '<?php echo $this->id;?>';
		out = '<a class="close" data-dismiss="alert" href="#">x</a>' + formatted_s + ' - ' + formatted_f + '</div>'+
			'<input type="hidden" name="jform[fields][<?php echo $this->id;?>][]" id="start_date<?php echo $this->id;?>" value="' + hidden_s + '" />' +
			'<input type="hidden" name="jform[fields][<?php echo $this->id;?>][]" id="end_date<?php echo $this->id;?>" value="' + hidden_f + '" />';
		out = '<div id="date'+ id+'" class="alert alert-info">'+out+'</div>';
		$('field_container<?php echo $this->id;?>').set('html', out);
	}
});
</script>