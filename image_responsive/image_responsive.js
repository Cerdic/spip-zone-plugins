function charger_url_image_responsive(this_img) {
	var dPR = window.devicePixelRatio;
		var src = this_img.attr("data-src");
		var l = this_img.attr("data-l");
		var h = this_img.attr("data-h");
		var w= parseInt(this_img.width());

		
		// Si l'image est trop petite, c'est pas la peine de demander trop grand…
		if (w > l) {
			w = l;
			dpr = false;
		}
		
		
		if (w == 0) {
		
		} else {
		
			if(dPR && dPR > 1) {
				
			} else {
				dPR = false;
			}
			
			if (htactif) {
				racine = src.substr(0, src.length-4);
				terminaison = src.substr(src.length-3, 3);
				var url_img = racine+"-resp"+w;
				if (dPR) url_img = url_img + "-"+dPR;
				url_img = url_img + "."+terminaison;
			} else {
				var url_img = "index.php?action=image_responsive&img="+src+"&taille="+w;
				if (dPR) url_img = url_img + "&dpr="+dPR;
			}
			this_img.attr("src", url_img).height("").removeAttr("data-top");
		}

}

function calculer_top_image_responsive(this_img) {
	this_img.attr("data-top", this_img.offset().top);
}

function charger_image_lazy() {
	var top = $(window).scrollTop();
	var height = $(window).height();
	
	var limite_haut = top - height;
	if (limite_haut < 0) limite_haut = 0;
	
	var limite_bas = top + 1.5*height;


	$(".lazy[data-top]").each(function() {
		this_img = $(this);
		var h = this_img.attr("data-top");
		if (h <= limite_bas && h >= limite_haut) charger_url_image_responsive(this_img);
	});	
}

function charger_image_responsive () {

	// Premier passage: mettre les images inconnues au bon format
	$("img[src$='rien.gif']").each(function() {
		var this_img = $(this);
		var l = this_img.attr("data-l");
		var h = this_img.attr("data-h");
		var w= parseInt(this_img.width());
		
		if (w > 0) {
			var hauteur = Math.round(h*w/l);
			this_img.height(hauteur);
		}
	});
	
	// Calculer le "top" des images lazy
	$(".lazy").each(function() {
		calculer_top_image_responsive($(this));
	});
	
	// Remplacer les URL non lazy
	$(".image_responsive:not('.lazy')").each(function() {
		charger_url_image_responsive($(this));
	});
	charger_image_lazy();


}
var timeout_charger_image_responsive;
$(document).ready(function() {
	charger_image_responsive();
});
// Plus rattrapage:
$(document).on("ajaxComplete", function() {
	timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
});

$(window).on("resize load",function() {
	timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
});
$(window).on("scroll touchmove", charger_image_lazy);