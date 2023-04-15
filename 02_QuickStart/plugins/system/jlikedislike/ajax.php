<?php
/**
 * @encoding     UTF-8
 * @version      ajax.php 21 січ 2014 12:23:06 drinkmaker
 * @package      Joomla
 * @copyright    Copyright (C) 2012 Open Source Matters. All rights reserved.
 * @license      GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * @author       Khmelnytsky Alexandr (drinkmaker@mail.ru)
 */

//Пинаем джумлу, что бы работать в её окружении
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__) . '/../../..' );
define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
JFactory::getApplication('site')->initialise();

$jinput = JFactory::getApplication()->input;
$vote = $jinput->get('vote', '', INTEGER );//Рейтинг
$rid = $jinput->get('rid', '', STRING);//ID рейтинга

if(strlen($rid)!=32){
    $answer = array('status' => 0, 'description' => "Ошибка: Не верный RID.");
    echo json_encode($answer);
    jexit();
}

$ip = $_SERVER['REMOTE_ADDR'];//Поулчаем IP голосовавшего

//Проверяем, а не голосовал ли уже этот IP за этот RID
$sql = "SELECT COUNT(*) FROM #__jlikedislike WHERE rid = '$rid' AND ip= '$ip';";
JFactory::getDbo()->setQuery($sql);
$already_voted = JFactory::getDbo()->loadResult();
if($already_voted != "0"){
    $answer = array('status' => -1, 'description' => "С Вашего IP-адреса уже голосовали.");
    echo json_encode($answer);
    jexit();
}

if($vote== 1){ $sql = "INSERT #__jlikedislike (rid, val, ip) VALUES ('$rid',  1, '$ip');"; }//Голос ЗА
if($vote==-1){ $sql = "INSERT #__jlikedislike (rid, val, ip) VALUES ('$rid', -1, '$ip');"; }//Голос ПРОТИВ

JFactory::getDbo()->setQuery($sql);
JFactory::getDbo()->execute();
$answer = array('status' => 1, 'description' => "Спасибо за ваш голос.");
echo json_encode($answer);