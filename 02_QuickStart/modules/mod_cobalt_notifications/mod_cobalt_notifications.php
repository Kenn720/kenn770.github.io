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
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php');
$Itemid = JRequest::getInt('Itemid');
$headerText = trim($params->get('header_text'));
$footerText = trim($params->get('footer_text'));

$lang = JFactory::getLanguage();
$lang->load('com_cobalt', JPATH_ROOT);

require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'helpers'. DIRECTORY_SEPARATOR .'helper.php';

JTable::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'administrator'. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'tables');
JModelLegacy::addIncludePath(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'models');

$ids = array();
$list = modCobaltNotificationsHelper::getList($params);

if (!count($list) && $params->get('template', 'default.php') == 'default.php')
	return FALSE;

foreach($list as $item)
{
	$ids[] = $item->id;
}

$sections = $params->get('section_id', 0);
if(is_array($sections))
	$sections = implode(',', $sections);

require JModuleHelper::getLayoutPath('mod_cobalt_notifications', $params->get('layout', 'default'));
