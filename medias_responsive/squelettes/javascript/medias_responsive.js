
function calculer_spip_documents() {

	$(".spip_documents").each(function() {
		var t = $(this);
		
		var width = t.attr("data-w");
		
		var parent = t.parent().width();
		
		if (width > parent) t.width("auto");
		else t.width(width);
		
		if ( t.hasClass("spip_documents_right") || t.hasClass("spip_documents_left") ) {
			if (width > 0.6*parent) t.addClass("spip_documents_center_forcer");
			else t.removeClass("spip_documents_center_forcer");
			
		}
		
	});

}


$(document).ready(calculer_spip_documents);
$(window).smartresize(calculer_spip_documents);