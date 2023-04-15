<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$default = $this->default;
?>

<?php if($this->params->get('params.total_limit') != 1):?>
<select name="filters[<?php echo $this->key;?>][by]" data-original-title="<?php echo JText::_('CSELECTFILTERCONDITION')?>" rel="tooltip">
	<option value="any" <?php if($this->value && $this->value['by'] == 'any') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASANYVALUE')?></option>
	<option value="all" <?php if($this->value && $this->value['by'] == 'all') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASALLVALUES')?></option>
</select>
<Br>
<?php endif;?>

<?php
foreach($default as $i => $value)
{
	$label = $this->_getVal($value);
	$default[$i] = JHtml::_('mrelements.autocompleteitem', $label, $value, $label);

}

$options['coma_separate'] = 0;
$options['only_values'] = 1;
$options['min_length'] = $this->params->get('params.min_length', 1);
$options['max_result'] = $this->params->get('params.max_result', 10);
$options['case_sensitive'] = $this->params->get('params.case_sensitive', 0);
$options['highlight'] = $this->params->get('params.highlight', 1);
$options['max_items'] = $this->params->get('params.max_items', 0);
$options['unique'] = $this->params->get('params.unique', 0);

$options['ajax_url'] = 'index.php?option=com_cobalt&task=ajax.field_call&tmpl=component';
$options['ajax_data'] = "field_id: {$this->id}, func: 'onFilterGetValues', field: '{$this->type}', section_id: {$section->id}";
echo JHtml::_('mrelements.listautocomplete', "filters[{$this->key}][value]", "filter_" . $this->id, $default, array(), $options);
?>
