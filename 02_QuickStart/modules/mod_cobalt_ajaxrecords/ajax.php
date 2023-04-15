<?php
/**
 * @encoding     UTF-8
 * @version      1.0.0
 * @copyright    Copyright (C) 2014 Alexandr Khmelnytsky (http://alexander.khmelnitskiy.ua). All rights reserved.
 * @license      GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * @author       Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua) 
 */

// For Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start Joomla
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(__FILE__) . '/../..' );//Путь к корню Joomla
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
JFactory::getApplication('site')->initialise();

$module = JModuleHelper::getModule('mod_cobalt_ajaxrecords');
$params = new JRegistry($module->params);

// Cobalt logic
include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'api.php';
$app = JFactory::getApplication();

$section_id = $app->input->getInt('section_id');

$user_id = $cat_id = NULL;
$user = JFactory::getUser();

$author = $app->input->get('user_id');
if (!$author && $app->input->get('option') == 'com_cobalt' && $app->input->getCmd('view') == 'record' && $app->input->getInt('id')) {
    $record = ItemsStore::getRecord($app->input->getInt('id'));
    $author = $record->user_id;
}

if (!$author && $app->input->get('option') == 'com_jsn' && $app->input->getCmd('view') == 'profile' && $app->input->getInt('id')) {
    $author = $app->input->getInt('id');
}

switch ($params->get('user_restrict')) {
    case 1:
        $user_id = $user->get('id');
        break;
    case 2:
        $user_id = $author;
        break;
    case 3:
        $user_id = $author ? $author : $user->get('id');
        break;
    case 4:
        $user_id = $user->get('id', $author);
        break;
}

$user_require = array(
    "followed", "bookmarked", "rated", "commented",
    "unpublished", "visited", "hidden", "expire", "created"
);

if (in_array($params->get('view_what', 'all'), $user_require) && !$user_id) {
    return;
}

$db = JFactory::getDbo();
$cat_id = 0;
if ($params->get('cat_restrict') && $section_id == $params->get('section_id') && $app->input->getInt('cat_id')) {
    $cat_id = $app->input->getInt('cat_id');

    if ($params->get('cat_restrict') == 2) {
        $section = ItemsStore::getSection($section_id);
        if (!$section->params->get('general.records_mode')) {
            $sql = "SELECT lft, rgt FROM `#__js_res_categories` WHERE id = {$cat_id}";
            $db->setQuery($sql);
            $res = $db->loadObject();

            if ($res) {
                $cat_sql = "SELECT id FROM `#__js_res_categories`
					WHERE lft >= " . (int) $res->lft . " AND rgt <= " . (int) $res->rgt . "
					AND section_id = {$section_id}
					AND published = 1";
                $db->setQuery($cat_sql);
                $cats = $db->loadColumn();
                $cat_id = implode(',', $cats);
            }
        }
    }
}

if ($params->get('catids')) {
    $cat_id = $params->get('catids');
}

if ($params->get('force_itemid')) {
    $app->input->set('force_itemid', $params->get('force_itemid'));
}

$app->input->set('section_id', $params->get('section_id'));

$ids = array();

if ($params->get('view_what', 'all') == 'field_value') {
    $query = $db->getQuery(TRUE);
    $query->select('record_id');
    $query->from('#__js_res_record_values');
    $query->where('field_key = ' . $db->quote($params->get('field_src')));
    $query->where('field_value ' . str_replace('{0}', str_replace('[USERNAME]', $user->get('username'), $params->get('field_value')), $params->get('fvco', "= '{0}'")));

    $db->setQuery($query);
    $ids = $db->loadColumn();
    $ids[] = 0;
}

if ($params->get('view_what', 'all') == 'new_reviews') {
    if (!$user->get('id')) {
        return;
    }
    $query = $db->getQuery(TRUE);
    $query->select('id');
    $query->from('#__js_res_record');
    $query->where('parent_id IN(SELECT r.id FROM #__js_res_record AS r WHERE r.user_id = ' . $user->get('id') . ' AND r.section_id = ' . $params->get('rsection_id') . ')');
    $query->where('section_id = ' . $params->get('section_id'));

    $db->setQuery($query);
    $ids = $db->loadColumn();

    if (empty($ids)) {
        return;
    }
    $params->set('view_what', 'all');
}
if ($params->get('view_what', 'all') == 'last_created') {
    $query = $db->getQuery(TRUE);
    $query->select('id');
    $query->from('#__js_res_record');
    $query->where('ctime > NOW() - INTERVAL ' . $params->get('ndays', '5') . ' DAY');
    $query->where('published = 1');
    $query->where('hidden = 0');
    $query->where('section_id = ' . $params->get('section_id'));

    $db->setQuery($query);
    $ids = $db->loadColumn();

    if (empty($ids)) {
        return;
    }

    $params->set('view_what', 'all');
}

$api = new CobaltApi();
$result = $api->records(
        $params->get('section_id'), $params->get('view_what', 'all'), $params->get('orderby'), $params->get('types', 0), $user_id, $cat_id, $params->get('limit', 10), $params->get('tmpl'), FALSE, FALSE, $params->get('lang_mode', 0), $ids);

$app->input->set('force_itemid', NULL);
$app->input->set('section_id', $section_id);

if ($result['total'] == 0) {
    if ($params->get('norecords')) {
        echo '<div class="no-rec-msg">' . JText::_($params->get('norecords')) . '</div>';
    }

    return;
}

// Start magic
$ajaxlimit = $params->get('ajaxlimit', 2);

$jinput = JFactory::getApplication()->input;
$step = $jinput->get('mod_cobalt_ajaxrecords_step', '0', 'INT');

$skip = (int)$step*$ajaxlimit;

$count = 0;
$count2 = 0;

$ajax_arr = array(); // Array of Items to show on current step

foreach ($result['list'] AS $item) { $count++;
    
    //Skip alredy loaded records
    if( ($skip!=0) and ($count <= $skip) ) { continue; }
    
    //Show only next part of records
    if ($count2 < $ajaxlimit) { 
        $count2++; 
        $ajax_arr []= $item;
    } else { break; }
}

$tmpl = $params->get('tmpl');
$tmpl = explode('.', $tmpl);
$tmpl = $tmpl[0];

$dir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'records' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR;
if(!JFile::exists("{$dir}default_list_{$tmpl}.php")) {
    JError::raiseError(100, 'TMPL not found');
    return;
}

// Stupid class for work $this->items in list templates
Class ObamaStupid {
    public $items;
    public $content;
 
    function __construct($items, $tmpl) {
        $this->items = $items;
        ob_start();
        include JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'records' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . 'default_list_' . $tmpl . '.php';
        $this->content = ob_get_contents();
        ob_end_clean();
        $this->content = str_replace("/modules/mod_cobalt_ajaxrecords", "", $this->content);
    }
}

if($ajax_arr) {
    $nigger = new ObamaStupid ($ajax_arr, $tmpl);// TODO: more beutiful solution is needed
    echo $nigger->content;
}