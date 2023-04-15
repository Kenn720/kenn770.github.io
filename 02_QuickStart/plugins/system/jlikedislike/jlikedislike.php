<?php
/**
 * @encoding     UTF-8
 * @version      jlikedislike.php 20.01.2014 3:27:16 drinkmaker
 * @package      Joomla
 * @copyright    Copyright (C) 2012 Open Source Matters. All rights reserved.
 * @license      GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * @author       Khmelnytsky Alexandr (drinkmaker@mail.ru)
 */
defined('_JEXEC') or die('Restricted access');

class plgSystemJlikedislike extends JPlugin
{    
    public function onAfterRender() {
        //Несколько проверок, что бы понять где мы, работать плагину или нет
        if(JFactory::getApplication()->isAdmin()){ return; }
        if(JFactory::getDocument()->getType() != 'html'){ return; }
        if($context == 'com_finder.indexer'){ return; }
       
        $buffer = JResponse::getBody();//Получили текущий документ
        
        if(strpos($buffer, '{jlikedislike') === false){ return; }//Простая проверка нужно ли работать дальше
        
        //Добавляем скрипты и стили в head
        $style = "<link rel='stylesheet' href='/plugins/system/jlikedislike/jlikedislike.css' />";
        $script = "<script src='/plugins/system/jlikedislike/jlikedislike.js' type='text/javascript'></script>";
        $buffer = str_replace("</head>", $style.$script."</head>", $buffer);
        
        preg_match("/<body.*\/body>/s", $buffer, $body);//Выделили тело
        //Находим все {jlikedislike ..... } и помещаем в $matches
        //$matches[0] Полное совпадение по шаблону, 
        //$matches[1] уникальный идентификатор рейтинга
        $regex	= '/{jlikedislike\s+(.*?)}/i';
        preg_match_all($regex, $body[0], $matches, PREG_SET_ORDER);
        
        //Если найдены вхождения
        if($matches) {    
            foreach ($matches as $match) {
                $jld_id = md5(   mb_strtolower( trim($match[1]) )   );//Уникальный ID для нашего рейтинга
                
                //Получаем данные о рейтинге по ID
                $sql = "SELECT SUM(val) FROM #__jlikedislike WHERE rid = '$jld_id';";
                JFactory::getDbo()->setQuery($sql);
                $raiting = JFactory::getDbo()->loadResult();
                
                //Шаблон рейтинга
                $output = "
                        <div class='jlikedislike' id='".$jld_id."'>
                            <strong class='votes'>".(0+$raiting)."</strong>
                            <div class='vote'>
                                <a class='up' href='#'></a>
                            </div>
                           
                        </div>
                        ";
                
                //Заменяем {jlikedislike ..... } на вывод рейтинга
                $body = preg_replace("|$match[0]|", $output, $body, 1);
            }
            //Сохраняем новое тело
            $buffer = preg_replace("/<body.*\/body>/sD", $body[0], $buffer );

            //Возвращаем всё джумле
            JResponse::setBody($buffer);
        }
        return;
    }
    
    //Получаем текущее значение рейтинга по его ID
    public function GetRaiting ($rid){
        return 5;
    }
}
?>