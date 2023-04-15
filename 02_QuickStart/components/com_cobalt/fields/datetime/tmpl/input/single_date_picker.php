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

if ($this->params->get('params.time', 0))
	$format_js .= ' %H:%M';
?>

<div class="default_dates" id="field_container<?php echo $this->id;?>">
<?php if ($this->value) : ?>
<?php
	foreach($this->value as $k => $val) :
		$date = new CDate($val);
		$id = $date->format('%Y%m%d');
		$date = $this->_getFormatted($val, true);
		?>
		<div id="date<?php echo $this->id;?>-<?php echo $id;?>" class="alert alert-info">
			<a class="close" data-dismiss="alert" href="#">x</a>
			<?php echo $date;?>
			<input type="hidden" value="<?php echo $val;?>" name="jform[fields][<?php echo $this->id;?>][]">
		</div>
	<?php endforeach;?>
<?php endif; ?>
</div>

<a href="javascript: void(0);" class="btn btn-warning" id="date_toggle<?php echo $this->id;?>">
	<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/calendar-select.png" align="absmiddle" title="<?php echo JText::_('D_SELECTDATE');?>">
	<?php echo JText::_('D_CALENDAR');?>
</a>

<script type="text/javascript">
picker<?php echo $this->id;?> = new Picker.Date($('field_<?php echo $this->id;?>'), {
	toggle: $('date_toggle<?php echo $this->id;?>'),
	timePicker: <?php echo $this->params->get('params.time', 0);?>,
	columns: <?php echo $this->params->get('params.columns', 2);?>,
	positionOffset: {x: 5, y: 0},
	format: '<?php echo $this->format;?>',
	onSelect: function (date)
	{
		var formatted = date.format_php(this.options.format);
		var hidden = date.format('<?php echo $format_js;?>');
		id = '<?php echo $this->id;?>-' + hidden;
		out = '<a class="close" data-dismiss="alert" href="#">x</a>' + formatted +
			'<input type="hidden" name="jform[fields][<?php echo $this->id;?>][]" value="' + hidden + '" />';

		out = '<div id="date'+ id+'" class="alert alert-info">'+out+'</div>';
		$('field_container<?php echo $this->id;?>').set('html', out);
		lastid = <?php echo $this->id;?>;
	}
});
</script>
