/*
 * Javascript pour le menu deroulant sur MSIE
 *
 * adapte de http://www.htmldog.com/articles/suckerfish/dropdowns/example/
 *
 */
/*sfHover = function() {
	var sfEls = document.getElementById("nav").getElementsByTagName("li");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}*/
//if (window.attachEvent) window.attachEvent("onload", sfHover);

$(document).ready(function(){
	$('li',document.getElementById("gadget-rubriques")).hover(
		function(){$(this).addClass('sfhover')},
		function(){$(this).removeClass('sfhover')}
		);
});