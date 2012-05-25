$(document).ready(function() {
$("#nav ul ul ").css({display: "none"}); 

$("#nav li").hover(function(){
	/*$(this).css({'background': 'transparent'});*/
	$(this).find('ul:first').css({visibility: "visible",display: "none"}).fadeIn(333);
 },function(){
	/*$(this).css({'background': ''});*/
	$(this).find('ul:first').css({visibility: "hidden"});
   });


  // fade span
  jQuery('#nav li.menu-entree').append('<span class="hover"></span>').each(function () {
    var jQueryspan = jQuery('> span.hover', this).css('opacity', 0);
	  jQuery(this).hover(function () {
	    jQueryspan.stop().fadeTo(333, 1);
	  }, function () {
	    jQueryspan.stop().fadeTo(333, 0);
	  });
	});



});

