function charger_url_image_responsive(this_img) {
	var dPR = window.devicePixelRatio;
		var src = this_img.attr("data-src");
		var l = this_img.attr("data-l");
		var h = this_img.attr("data-h");

		if (this_img.hasClass("image_responsive_v")) {
			var vertical = true;
			var dim= parseInt(this_img.height());
		} else {
			var vertical = false;
			var dim= parseInt(this_img.width());
		}
		
		var tailles = this_img.attr("data-tailles");
					
		
		if (tailles) {
			var w_max = 0;
			var t = $.parseJSON(tailles.replace(/\\"/g, '"'));
			var changer_w = 1;
			
			$.each(t, function (index, value) {
				value = parseInt(value);
				//console.log(value + " " + d + " " + changer_w);
				if (changer_w == 1) w_max = value;
				if (value > dim) changer_w = 0;
			});
			// console.log ("Wmax: "+w_max);
			if (w_max > 0) dim = w_max;
		}
			// console.log ("W: "+dim);

			// console.log ("L: "+l);


		// Si l'image est trop petite, c'est pas la peine de demander trop grand…
		if (vertical && parseInt(dim) > parseInt(h)) {
			dim = h;
			dpr = false;
		} else if (parseInt(dim) > parseInt(l)) {
			dim = l;
			dpr = false;
		}

			//console.log ("Wapres: "+dim);
		
		if (dim == 0) {
		
		} else {
		
			if(dPR && dPR > 1) {
				
			} else {
				dPR = false;
			}
			
			if (htactif) {
				racine = src.substr(0, src.length-4);
				terminaison = src.substr(src.length-3, 3);
				var url_img = racine+"-resp"+dim;
				if (vertical) url_img = url_img + "v";
				if (dPR) url_img = url_img + "-"+dPR;
				url_img = url_img + "."+terminaison;
			} else {
				var url_img = "index.php?action=image_responsive&img="+src+"&taille="+dim;
				if (vertical) url_img = url_img + "v";
				if (dPR) url_img = url_img + "&dpr="+dPR;
			}
			this_img.attr("src", url_img).height("").width("").removeAttr("data-top");
		}

}

function calculer_top_image_responsive(this_img) {
	this_img.attr("data-top", this_img.offset().top);
}

function charger_image_lazy(sTop) {
	if (typeof(sTop) == 'undefined') var sTop = $(window).scrollTop();
	var hauteur = $(window).height();
	
	var limite_haut = sTop - hauteur;
	if (limite_haut < 0) limite_haut = 0;
	
	var limite_bas = sTop + 1.5*hauteur;

	

		//console.log(sTop);

	$(".lazy[data-top]").each(function() {
		this_img = $(this);
		var h = this_img.attr("data-top");
		
		
		if (h <= limite_bas && h >= limite_haut) charger_url_image_responsive(this_img);
	});	
}

function charger_image_responsive () {
	
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
$(window).on("scroll touchmove", function() {
	charger_image_lazy();
	});