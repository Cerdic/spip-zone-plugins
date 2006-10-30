/* Splickerbox - Code javascript
*
* Badge à la flickr, par BoOz booz AT rezo.net
*
* Fonctionne avec jQuery.
**/


$(document).ready(function(){

cptj =0;
max = $("td.image img").size();

//il faudrait essayer ce plugin .pause()
// http://www.mythin.net/pause.js

if(max>0){
start();
}

});


function start(){
setTimeout('$("td.image img").splicker('+cptj+');',1000);
}


$.fn.splicker = function(i) {
$("div#changeMe").css({
width: "70px",
height: "70px"
});
image = this.get(i).cloneNode(true) ;
image.style.width="100%";
image.style.height="100%";

href = this.get(i).parentNode.href;

$("div#changeMe").append(image);

$("div#changeMe img").wrap("<a href=\""+href+"\">","</a>").click(function(){showLightbox(href);});

if(i%3 == 0){
$("div#changeMe").css("left","35px");
ll1="70";
}
if(i%3 == 1){
$("div#changeMe").css("left","35px");
ll1="35";
}
if(i%3 == 2){
$("div#changeMe").css("left","0px");
ll1="0";
}

if(i>=0 && i<=2){
$("div#changeMe").css("top","70px");
tt1="105";
}
if(i>=3 && i<=5){
$("div#changeMe").css("top","35px");
tt1="70";

}
if(i>=6 && i<=8){
$("div#changeMe").css("top","0px");
tt1="35";
}
if(i>=9 && i<=11){
$("div#changeMe").css("top","0px");
tt1="0";
}

tt0 = $("div#changeMe").get(0).style.top;
ll0 = $("div#changeMe").get(0).style.left;
tt0 = tt0.replace(/px/,"");
ll0 = ll0.replace(/px/,"");

$("#statusMsg").html(tt0+'->'+ll0);

$("div#changeMe").fadeIn(2000);

setTimeout('$("div#changeMe").resize_(1500,35,'+tt0+','+tt1+','+ll0+','+ll1+');',4000);

setTimeout('itere();$("div#changeMe img").remove();start()',7000);

}


jQuery.fn.resize_ = function(a,w,t0,t1,l0,l1,o) {
o = jQuery.speed(a,o);
return this.each(function(){
jQuery(this).animate({top:t1,left:l1,width:w,height:w},o);
});
};


function itere () {
if(cptj == max-1){ cptj=0 ;}else{ cptj++ ;}	
$("#statusMsg").html("it"+cptj+"=?"+max);
};