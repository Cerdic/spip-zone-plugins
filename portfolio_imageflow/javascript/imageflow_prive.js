/*
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 */

$(document).ready(function(){
	
	// [@attr] style selectors removed in jQuery 1.3
	// @see: http://docs.jquery.com/Selectors
	// rendre compatible avec anciennes versions de jQuery
	var jqss = (jQuery.fn.jquery < '1.3') ? '@' : '';
	
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
	$(".reset-btn").hover(function(){
		$(this).css("cursor", "pointer");
	},function(){
		$(this).css("cursor", "auto");
	});
	$(".reset-btn").click(function(){
		$("#imageflow_configure .slider").removeClass('checked');
		$("#imageflow_configure .slider input").removeAttr('checked');
		for (key in imageflow_default) {
			if((key == 'preloader') || (key == 'slideshow') || (key == 'slider')) {
				$("#imageflow_configure [" + jqss + "name="+key+"]").removeAttr('checked');
			}
			else if (key != 'slider') {
				$("#imageflow_configure [" + jqss + "name="+key+"]").val(imageflow_default[key]);
			}
			else if (key == 'slider') {
				$("#imageflow_configure [" + jqss + "value=" + imageflow_default[key] + "]").attr('checked', 'checked');
				$("#imageflow_configure [" + jqss + "value=" + imageflow_default[key] + "]").parent().addClass('checked');
			}
		}
	});
	
	$("input[" + jqss + "name=active_description]").click(function(){
		$("#label_active_desc_effets").toggle();
		$("#label_active_alert").toggle();
	});
});
