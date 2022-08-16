/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function cover_uncover(id_text, id_button) {
    var text = document.getElementById(id_text);
    var button = document.getElementById(id_button);

    if(text.type === "password") {
        text.type = "text";
        button.innerHTML = "隠す";
    } else {
        text.type = "password";
        button.innerHTML = "表示";
    };    
};


