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
				$("#imageflow_configure [@name="+key+"]").removeAttr('checked');
			}
			else if (key != 'slider') {
				$("#imageflow_configure [@name="+key+"]").val(imageflow_default[key]);
			}
			if (key == 'slider') {
				$("#imageflow_configure [@value=" + imageflow_default[key] + "]").attr('checked', 'checked');
				$("#imageflow_configure [@value=" + imageflow_default[key] + "]").parent().addClass('checked');
			}
		}
	});
});
