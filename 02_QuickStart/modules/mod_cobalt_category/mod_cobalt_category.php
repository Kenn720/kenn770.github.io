<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
include_once JPATH_ROOT . '/components/com_cobalt/api.php';
require_once(dirname(__FILE__) . '/helper.php');

$app = JFactory::getApplication();
$Itemid = $app->input->getInt('Itemid');
$headerText = trim($params->get('header_text'));
$footerText = trim($params->get('footer_text'));

JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_cobalt/models');

$cat_id = $params->get('init_cat');
if(!$cat_id && $app->input->getInt('cat_id') && $params->get('mode', 2) == 1 &&
	$app->input->getCmd('option') == 'com_cobalt' && $app->input->getInt('section_id') == $params->get('section_id')
)
{
	$cat_id = $app->input->getInt('cat_id');
}

$rid = 0;
if($app->input->get('option') == 'com_cobalt' && $app->input->get('view') == 'record')
{
	$rid = $app->input->getInt('id');
}

$categories = modCobaltCategoriesHelper::getList($params, $cat_id);
$section = ItemsStore::getSection($params->get('section_id'));

$section->records = null;
if($params->get('records'))
{
	if($cat_id)
	{
		$section->records = modCobaltCategoriesHelper::getCatRecords($cat_id, $params);
	}
	else
	{
		$section->records = modCobaltCategoriesHelper::getSectionRecords($params);
	}
}

$parents = modCobaltCategoriesHelper::getParentsList($cat_id);
$parents[] = $params->get('section_id');

require JModuleHelper::getLayoutPath('mod_cobalt_category', $params->get('layout', 'default'));