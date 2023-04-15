<?php
/**
 * @encoding     UTF-8
 * @version      1.0.0
 * @copyright    Copyright (C) 2014 Alexandr Khmelnytsky (http://alexander.khmelnitskiy.ua). All rights reserved.
 * @license      GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * @author       Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua) 
 */

defined('_JEXEC') or die('Restricted access');

$moduleclass_sfx            = htmlspecialchars($params->get('moduleclass_sfx'));
$modName = pathinfo(__FILE__)['filename'];

JHtml::_('jquery.framework');

$step = 0;
$ajax_url = JURI::root().'modules/mod_cobalt_ajaxrecords/ajax.php?mod_cobalt_ajaxrecords_step=';
$mod_result = file_get_contents($ajax_url.$step);

require JModuleHelper::getLayoutPath($modName, $params->get('layout', 'default'));