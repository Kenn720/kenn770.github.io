<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$params =  $this->catsel_params;
$list = $default = array();
$i =  0;

$cats_model = JModelLegacy::getInstance('Categories', 'CobaltModel');
$cats_model->section = $this->section;
$cats_model->parent_id = 1;
$cats_model->order = 'c.lft ASC';
$cats_model->levels = 1000;
$cats_model->all = 1;
$categories = $cats_model->getItems();

if(!$categories)
{
	echo JText::_('CSECTIONNOCATEGORIES');
	return;
}

if(!is_array($this->default_categories))
	settype($this->default_categories, 'array');

$default = $cats_model->getCategoriesById($this->default_categories);
$default_cats = array();
foreach ($default AS $cat)
{
	$default_cats[$cat->id] = $cat->title;
}
foreach ($default_cats as $id => $cat)
{
	$default[$i]->id = $id;
	$default[$i]->plain = $cat;
	$default[$i]->html = $cat;
	$i++;
}
$pattern = '<span class="list-element">%s%s<br /><span class="small">%s</span>%s';
foreach ($categories as $i => $cat)
{
	if(in_array($cat->id, $this->not_allow_cats))
	{
		continue;
	}
	if($cat->params->get('submission', 1) == 0)
	{
		continue;
	}
	
	$img = $descr = NULL;
	$list[$i] = new stdClass();
	$list[$i]->id = $cat->id;
	$list[$i]->plain = $cat->title;
	$list[$i]->html = $cat->title;
	
	
	if($params->get('tmpl_params.cat_img') && $cat->image)
	{
		$img = '<img src="'.JURI::root(TRUE).'/'.$cat->image.'" style="max-width:'.$params->get('tmpl_params.cat_img_width', 50).'px;" hspace="5" align="absmiddle">';
	}
	
	if($params->get('tmpl_params.cat_descr') && $cat->description)
	{
		$descr = '<br /><span class="small">'.substr(trim(strip_tags($cat->{'descr_'.$params->get('tmpl_params.cat_descr')})), 0, $params->get('tmpl_params.cat_descr_length', 200)).'</span>';
	}
	$list[$i]->render = sprintf($pattern, $img, $cat->title, $cat->path, $descr); 
}

$options['coma_separate'] = 0;
$options['only_values'] = 1;
$options['min_length'] = 1;
$options['case_sensitive'] = 0;
$options['unique'] = 1;
$options['highlight'] = 1;

$options['max_result'] = $this->catsel_params->get('tmpl_params.cat_max_result', 10);
$options['max_width'] = $this->catsel_params->get('tmpl_params.max_width', 400);
$options['min_width'] = $this->catsel_params->get('tmpl_params.min_width', 300);
$options['max_items'] = $limit = $this->type->params->get('submission.multi_category', 0) ? $this->type->params->get('submission.multi_max_num', 1) : 1;

if($limit > 1)
{
	echo '<div><small>'.JText::sprintf('CSELECTLIMIT', $limit).'</small></div>';
}	
?>

<style>
.list-element IMG {
	float: left;
	padding-right: 8px !important;
}
</style>
<?php echo JHtml::_('mrelements.listautocomplete', "jform[category]", "category", $default, $list, $options);?>
