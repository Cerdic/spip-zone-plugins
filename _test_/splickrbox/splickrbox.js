/* Splickerbox - Code javascript
*
* Badge à la flickr, par BoOz booz AT rezo.net
*
* Fonctionne avec jQuery.
**/


/*
 * //parametrage */

//exemple 

// cote = 70px -> 35px
// cote = 100px -> 50px
// cote = 124px -> 62px

var cote = 100 ;

/*
********/




var petit_cote = cote/2 ;

$(document).ready(function(){

max = $("td.image img").size();
cptj =0;

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
$("div#changeMe").css("width", cote + "px");
$("div#changeMe").css("height", cote + "px");
//alert($("div#changeMe").css("width"));

image = this.get(i).cloneNode(true) ;
image.style.width="100%";
image.style.height="100%";

href = this.get(i).parentNode.href;

$("div#changeMe").append(image);

$("div#changeMe img").css("cursor","pointer").click(function(){
	//thickbox
	if(typeof imageArray != 'undefined'){
	TB_on();
	TB_show('',href,'image');
	}else{
	window.document.location = href ;
	}

});


if(i%3 == 0){
$("div#changeMe").css("left","0px");
left1="0";
}
if(i%3 == 1){
$("div#changeMe").css("left", petit_cote + "px");
left1= petit_cote ;
}
if(i%3 == 2){
$("div#changeMe").css("left", petit_cote + "px");
left1= 2*petit_cote ;
}

if(i>=0 && i<=2){
$("div#changeMe").css("top","0px");
top1="0";
}
if(i>=3 && i<=5){
$("div#changeMe").css("top","0px");
top1=petit_cote;
}
if(i>=6 && i<=8){
$("div#changeMe").css("top",2*petit_cote + "px");
top1=2*petit_cote;
}
if(i>=9 && i<=11){
$("div#changeMe").css("top", 2*petit_cote + "px");
top1= 3*petit_cote ;
}

top0 = $("div#changeMe").get(0).style.top;
left0 = $("div#changeMe").get(0).style.left;
top0 = top0.replace(/px/,"");
left0 = left0.replace(/px/,"");

$("#statusMsg").html(top0+'->'+left0);

$("div#changeMe").fadeIn(2000);

setTimeout('$("div#changeMe").resize_(1500,'+petit_cote+','+top0+','+top1+','+left0+','+left1+');',4000);

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