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
$doc = JFactory::getDocument();
$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mask/Meio.Mask.js');
$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mask/Meio.Mask.Fixed.js');
/*	$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mask/Meio.Mask.Reverse.js');
$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mask/Meio.Mask.Repeat.js');
$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mask/Meio.Mask.Regexp.js');*/
$doc->addScript(JURI::root(TRUE) . '/media/mint/js/mask/Meio.Mask.Extras.js');

$class = ' class="' . $this->params->get('core.field_class', 'inputbox') . ($this->required ? ' required' : NULL) . '" ';
$required = $this->required ? 'required="true" ' : NULL;

$format_js = '%Y-%m-%d';

$mask = array();
if ($this->params->get('params.input_order', 'month') == 'month')
{
	$mask[] = '1m';
	$mask[] = '3d';
	$format = 'm-d-Y H:i';
	$format_js = sprintf('%%m%s%%d%s%%Y',$this->params->get('params.input_delimiter', '/'), $this->params->get('params.input_delimiter', '/') );
}
else
{
	$mask[] = '3d';
	$mask[] = '1m';
	$format = 'd-m-Y H:i';
	$format_js = sprintf('%%d%s%%m%s%%Y',$this->params->get('params.input_delimiter', '/'), $this->params->get('params.input_delimiter', '/') );
}
$mask[] = '2999';
$mask = implode($this->params->get('params.input_delimiter', '/'), $mask);
if ($this->params->get('params.time', 0))
	$mask .= ' 2h:59';

$default = ($this->value && strtotime($this->value[0])) ? $this->value[0] : $this->default;
if($default != '')
{					
	$default = date(str_replace('%', '', $format_js), strtotime($default));
}

if ($this->params->get('params.time', 0))
	$format_js .= ' %H:%M';
?>
<input type="text" value="<?php echo $default;?>" name="jform[fields][<?php echo $this->id;?>][]" id="field_<?php echo $this->id;?>" 
	<?php echo $class.$required;?> 
	data-field-options="{type: '<?php echo $this->params->get('params.input_order', 'month');?>', delimiter: '<?php echo $this->params->get('params.input_delimiter', '/');?>'}"
 	data-meiomask-options="{mask: '<?php echo $mask;?>',  removeIfInvalid: true}" data-meiomask="fixed"/>

<script type='text/javascript'>					
	$('field_<?php echo $this->id;?>').meiomask($('field_<?php echo $this->id;?>').get('data-meiomask'), JSON.decode($('field_<?php echo $this->id;?>').get('data-meiomask-options')));					
</script>
			
