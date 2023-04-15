/**
 * @encoding     UTF-8
 * @version      jlikedislike.js 21.01.2014 3:27:16 drinkmaker
 * @package      Joomla
 * @copyright    Copyright (C) 2012 Open Source Matters. All rights reserved.
 * @license      GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * @author       Khmelnytsky Alexandr (drinkmaker@mail.ru)
 */
var jQ = false;
function initJQ(){if(typeof(jQuery)=='undefined'){if(!jQ){jQ = true;document.write('<scr'+'ipt type="text/javascript"src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></scr'+'ipt>');}setTimeout('initJQ()',50);}else{(function($){
$(function(){
    //Нажатие на кнопки "ЗА" и "ПРОТИВ"
    $(".jlikedislike .up, .jlikedislike .down").click(function (e){
        e.preventDefault();
        var btns = $(this).parent();
        var rid  = $(this).parent().parent().attr("id");
        if(btns.hasClass("disable")){ return; }//Выходим, если кнопки отключены
        if($(this).hasClass("down")){
            SendVote(-1, btns, rid);//Оправлем голос ПРОТИВ
        }else{
            SendVote(1, btns, rid);//Оправлем голос ЗА
        }
    });
    
    //Отправляет голос и обрабатывает ответ
    function SendVote (vote, btns, rid){
        $.post( "/plugins/system/jlikedislike/ajax.php?vote="+vote+"&rid="+rid)
            .done(function(data) {
                var answer = jQuery.parseJSON(data);
                if(answer['status'] == 1){
                    btns.prev().text( (parseInt(btns.prev().text())+vote) );//Нарастили счётчик   
                }
                btns.addClass("disable");//Отключили кнопочки
            }).fail(function(data) {
                var answer = jQuery.parseJSON(data);
                console.log("error: "+answer['description']);
            }).always(function(data) {
                var answer = jQuery.parseJSON(data);
                btns.next().text(answer['description']);//Показали ответ
            });        
    }
});})(jQuery);}}initJQ();