<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$params = $this->tmpl_params['cindex'];

$cats_model = $this->models['categories'];
$cats_model->section = $this->section;
$cats_model->parent_id = 1;
$cats_model->order = 'c.lft ASC';
$cats_model->levels = 100;
$cats_model->all = 1;
$cats_model->nums = ($params->get('tmpl_params.cat_nums') || $params->get('tmpl_params.subcat_nums') || !$params->get('tmpl_params.cat_empty', 1));
$categories = $cats_model->getItems();

$cats[] = JHtml::_('select.option', JRoute::_(Url::records($this->section)), $this->section->name);

foreach($categories as $cat)
{
	if($params->get('tmpl_params.cat_empty', 1) || (!$params->get('tmpl_params.cat_empty', 1) && ($cat->num_current || $cat->num_all)))
	{
		$name   = str_repeat('|-- ', $cat->level) . $cat->title;
		$name   = $params->get('tmpl_params.cat_nums', 1) ? $name . " ({$cat->records_num})" : $name;
		$cats[] = JHtml::_('select.option', JRoute::_(Url::records($this->section, $cat->id)), $name);
	}
}

if(!count($cats))
{
	return;
}
$default = empty($this->category->id) ? NULL : JRoute::_(Url::records($this->section, $this->category));

if($params->get('tmpl_core.show_title', 1)) {
	echo '<h3>'.JText::_($params->get('tmpl_core.title_label', 'Category Index')).'</h3>';
}

echo JHtml::_('select.genericlist', $cats, 'selectcat', NULL, 'value', 'text', $default);
?>
<script type="text/javascript">
	(function($) {
		$('#selectcat').bind('change keyup', function() {
			var val = $(this).val();
			window.location = val;
		});
	}(jQuery))
</script>