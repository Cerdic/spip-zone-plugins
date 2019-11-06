
function calculer_spip_documents() {

	$(".spip_documents").each(function() {
		var t = $(this);
		
		var width = t.attr("data-w");
		
		var parent = t.parent().innerWidth();
		
		if (width > parent) t.width("auto");
		else t.width(width);
		
		if ( t.hasClass("spip_documents_right") || t.hasClass("spip_documents_left") ) {	
			console.log(width+" - " + parent);
			if (width > 0.6*parent) t.addClass("spip_documents_center_forcer").css("width", "auto");
			else t.removeClass("spip_documents_center_forcer").css("width", width+"px");
			
		}
		
		if (t.hasClass("kenburns")) {
			calculer_top_documents_actifs(t, "kenburns");
		}
		if (t.hasClass("spip_documents_flip")) {
			calculer_top_documents_actifs(t, "flip");
		}
		
	});

}

function calculer_top_documents_actifs(this_img, type) {
	var offset = this_img.offset().top;
	this_img.attr("data-top-"+type, offset);
}


function _declencher_documents_actifs() {
	if (scrollT) var sTop = scrollT;
	else var sTop = $(window).scrollTop();

	var hauteur = $(window).height();
	
	var limite_haut = sTop - 0.2*hauteur;
	if (limite_haut < 0) limite_haut = 0;
	
	var limite_bas = sTop + 0.8*hauteur;

	$(".kenburns:not(.kenburns_actif)").each(function() {
		this_img = $(this);
		var h = this_img.attr("data-top-kenburns");
		if (h <= limite_bas && h >= limite_haut) this_img.addClass("kenburns_actif");
	});	
	$(".spip_documents_flip:not(.spip_documents_flip_actif)").each(function() {
		this_img = $(this);
		var h = this_img.attr("data-top-flip");
		if (h <= limite_bas && h >= limite_haut) this_img.addClass("spip_documents_flip_actif");
	});	


}


$(document).ready(calculer_spip_documents);
$(window).smartresize(calculer_spip_documents);

$(window).on("load scroll touchmove",declencher_documents_actifs);
$(document).on("ajaxComplete", declencher_documents_actifs);


var didScroll_documents_actifs = false

function declencher_documents_actifs () {
	didScroll_documents_actifs = true;
}
function render_documents_actifs() {
	if(didScroll_documents_actifs) {
		didScroll_documents_actifs = false;
		_declencher_documents_actifs()
    }
}

(function animloop_documents_actifs(){
  requestAnimationFrame(animloop_documents_actifs);
  render_documents_actifs();
})();

