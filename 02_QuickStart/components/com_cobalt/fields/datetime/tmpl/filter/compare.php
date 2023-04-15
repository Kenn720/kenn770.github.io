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
$default = new JRegistry($this->value);
$opt[] = JHtml::_('select.option', '1', JText::_($this->params->get('params.filter_compare_stbefore')), 'id', 'title');
$opt[] = JHtml::_('select.option', '2', JText::_($this->params->get('params.filter_compare_stafter')), 'id', 'title');
$opt[] = JHtml::_('select.option', '3', JText::_($this->params->get('params.filter_compare_endbefore')), 'id', 'title');
$opt[] = JHtml::_('select.option', '4', JText::_($this->params->get('params.filter_compare_endafter')), 'id', 'title');
$compare = JHtml::_('select.genericlist', $opt, 'filters[' . $this->key . '][condition]', '', 'id', 'title', $default->get('condition', 1));

$value = (isset($this->value['date'])) ? $this->_getFormatted($this->value['date'], true) : '';
?>
<?php echo JText::sprintf($this->params->get('params.filter_compare_label'), $compare);?> 

<input type="text" class="cdate-field" value="<?php echo $value;?>" id="field_<?php echo $this->id;?>" size="30">
<input type="hidden" name="filters[<?php echo $this->key;?>][date]" value="<?php echo $default->get('date');?>" id="filter_<?php echo $this->id;?>">

<script type="text/javascript">
	picker<?php echo $this->id;?> = new Picker.Date(document.getElementById('field_<?php echo $this->id;?>'), {
		timePicker:  <?php echo $this->params->get('params.time', 0);?>,
		columns: <?php echo $this->params->get('params.columns', 2);?>,
		positionOffset: {x: 5, y: 0},
		format: '<?php echo $this->format;?>',
		onSelect: function (date)
		{
			var hidden = date.format('<?php echo $format_js;?>');
			var formatted = date.format_php('<?php echo $this->php_format; ?>');
			jQuery('#filter_<?php echo $this->id;?>').val(hidden);
			jQuery('#field_<?php echo $this->id;?>').val(formatted);
		}
	});	
</script>
