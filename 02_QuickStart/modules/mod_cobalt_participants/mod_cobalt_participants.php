<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

if($app->input->getCmd('option') != 'com_cobalt') return;
if($app->input->getCmd('view') != 'record') return;
if(!$app->input->getInt('id')) return;

$id = $app->input->getInt('id');

include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'php'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'helper.php';

$db = JFactory::getDbo();
$query = "SELECT user_id FROM #__js_res_hits WHERE user_id > 0 AND record_id = {$id} GROUP BY user_id";
$db->setQuery($query, 0, $params->get('limit', 10));
$list = $db->loadColumn();

if(!$list) return;

$record = ItemsStore::getRecord($id);
$section = ItemsStore::getSection($record->section_id);

require JModuleHelper::getLayoutPath('mod_cobalt_participants', $params->get('layout', 'default'));
