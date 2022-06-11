/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function(){
    
    $(window).on("beforeunload", function(){
        return true;  //メッセージを出す。
    });

    $("#submit").on("click", function(){
        $(window).off("beforeunload");
    });
    
});




