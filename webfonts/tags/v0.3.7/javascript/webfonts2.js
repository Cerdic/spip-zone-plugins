/**
@function autoResize
@desc Resize an iframe acoording to content
@author Mist. GraphX mistergraphx@gmail.com
@param {int} id block id to be resized
@return String
*/
function autoResize(id){
    var newheight;
    var newwidth;

    if(document.getElementById){
        newheight=document.getElementById(id).contentWindow.document .body.scrollHeight;
        newwidth=document.getElementById(id).contentWindow.document .body.scrollWidth;
    }
    document.getElementById(id).style.height= (newheight) + "px";
}