/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

const id_form = document.getElementById("form_mr");
const id_submit = document.getElementById("submit");

function checkBU(e){
    e.preventDefault();  // ポップアップで警告を出す。
}

window.addEventListener('beforeunload', checkBU);

id_submit.addEventListener('click', function(e){
    removeEventListener('beforeunload', checkBU); // submitの時は警告を出さない。
});



