/*************************************
 * css instable: rend votre css instable
 * 
 * requiert jQuery
 *   
 * auteur:  erational (http://www.erational.org) 
 * version: 0.1
 * date:    2007.11.25
 * licence: GPL 2.0 
 ***************************************/

 
$(document).ready(function(){
  css_instable();
});

//
// parametre (a placer ds cfg ?)
// 
var css_instable_tempo =  100; // en ms

//
// css_instable
// 
// documentation DOM:CSS: http://developer.mozilla.org/en/docs/DOM:CSS

function css_instable() { 
  if (getRandomInt(0,1))  var nodes = $("div");
                   else   var nodes = new Array("body","a","p","div","span","img","ul","ol","li","hr");

  var i = Math.round((nodes.length-1)*Math.random());
  var cssObj = {
        backgroundColor: "rgb("+getRandomInt(0,255)+","+getRandomInt(0,255)+","+getRandomInt(0,255)+") !important",
        color: "rgb("+getRandomInt(0,25)+","+getRandomInt(0,255)+","+getRandomInt(0,255)+") !important" ,
        backgroundImage: "none" /*,
        paddingLeft: getRandomInt(0,25)+"px",
        paddingRight: getRandomInt(0,25)+"px",
        paddingTop: getRandomInt(0,25)+"px",
        paddingBottom: getRandomInt(0,25)+"px", 
        fontSize: getRandomInt(80,120)+"%"  */
  }
  $(nodes[i]).css(cssObj);  
  window.setTimeout(css_instable,css_instable_tempo);

} // css_instable


//
// generate a random number between min and max
// 
function getRandomInt(min, max){
  return Math.floor(Math.random() * (max - min + 1)) + min;
} // getRandomInt

