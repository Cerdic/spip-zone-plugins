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
// et essayer de redemmarer le slide show apres la derniere image

while(cptj <= max) {
setTimeout('$("td.image img").splicker('+cptj+');',6000 + cptj*6000);
//if(cptj==max) cptj=-1;
cptj++;
}

});

$.fn.splicker = function(i) {
image = this.get(i).cloneNode(true) ;
href = this.get(i).parentNode.href;
$("div#changeMe").append(image);
$("div#changeMe img").wrap("<a href=\""+href+"\">","</a>").click(function(){showLightbox(href);});
$("div#changeMe img").showCustom("slow",70);
if(i>=0 && i<=2){
$("div#changeMe").css("top","70px");
}
if(i>=3 && i<=5){
$("div#changeMe").css("top","35px");
}
if(i>=6 && i<=8){
$("div#changeMe").css("top","0px");
}

setTimeout('$("div#changeMe img").hide_propre();',5000);
}


$.fn.hide_propre = function() {
this.hide("slow", function(){
        $(this).remove();
      });

}


$.fn.showCustom = function(a,w,o) {
o = $.speed(a,o);
return this.each(function(){
(new fx.Opacity(this,o)).show();
(new fx.Width(this,o)).custom(0,w);
(new fx.Height(this,o)).custom(0,w);
});
};