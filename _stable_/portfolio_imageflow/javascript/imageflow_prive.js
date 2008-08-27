/*
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 */

$(document).ready(function(){
	
	$(".slider").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});

	$(".slider").click( function() {
		$("#sliders").children("input").removeAttr("checked");
		$("#sliders").children(".slider").removeClass("checked");
		$(this).addClass("checked");
		$(this).children("input").attr("checked", "checked");
	});
});
