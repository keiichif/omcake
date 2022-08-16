/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

reloaded = true;
window.addEventListener('focus', function(e){
    if (! reloaded){
        location.reload();
        reloaded = true;
    }
});
window.addEventListener('blur', function(e){
    reloaded = false;
});