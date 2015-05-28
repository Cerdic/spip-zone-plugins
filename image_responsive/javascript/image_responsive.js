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
		
		var forcer_zoom = this_img.parents("[data-zoom-responsive]").attr("data-zoom-responsive");
		if (forcer_zoom) dim = dim * forcer_zoom;
		
		var tailles = this_img.attr("data-tailles");
		
		if (tailles) {
			var w_max = 0;
			var t = $.parseJSON(tailles.replace(/\\"/g, '"'));
			var changer_w = 1;
			
			$.each(t, function (index, value) {
				value = parseInt(value);
				//console.log(value + " " + dim + " " + changer_w);
				if (changer_w == 1) w_max = value;
				if (value >= dim) changer_w = 0;
			});
			 //console.log ("Wmax: "+w_max);
			if (w_max > 0) dim = w_max;
		}
			// console.log ("W: "+dim);

			// console.log ("L: "+l);
			
		var autorisees = this_img.attr("data-autorisees");					
		if (autorisees) {
			autorisees = $.parseJSON(autorisees.replace(/\\"/g, '"'));		
		}


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
					// si l'image d'origine n'est pas nettement plus grande que l'image demandée, 
					// ne pas passer dPR, sinon on récupère image de même taille mais trop compressée
					if (vertical && h < 1.5*dim) dPR = false;
					else if (l < 1.5*dim) dPR = false;
					// forcer à 2
					else dPR = 2;
					
				} else {
					dPR = false;
				}
								
				if (autorisees && autorisees[dim]) {
					if (dPR < 1.5) url_img = autorisees[dim][1];
					else url_img = autorisees[dim][2];
				}
				else {				
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
				}
			this_img.attr("src", url_img).height("").width("").removeAttr("data-top");
		}

}

function charger_url_background_responsive(this_img) {
	var dPR = window.devicePixelRatio;
		vertical = false;

		var dim_l= parseInt(this_img.width());
		var dim_h = parseInt(this_img.height());

		if (dim_l > dim_h) {	
			var mode = "i";
			var src = this_img.attr("data-italien-src");
			var l = this_img.attr("data-italien-l");
			var h = this_img.attr("data-italien-h");
		} else {
			var mode = "p";
			var src = this_img.attr("data-portrait-src");
			var l = this_img.attr("data-portrait-l");
			var h = this_img.attr("data-portrait-h");
		}

		
		if ( (dim_l/dim_h) > (l/h) ) { /* fenetre plus large que l'image */
			dim = dim_l;
		} else {
			dim = dim_h *l / h;
			var pourcent = "auto 100%";
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
				if (value >= dim) changer_w = 0;
			});
			// console.log ("Wmax: "+w_max);
			if (w_max > 0) dim = w_max;
		}



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
				// si l'image d'origine n'est pas nettement plus grande que l'image demandée, 
				// ne pas passer dPR, sinon on récupère image de même taille mais trop compressée
				if (vertical && h < 1.5*dim) dPR = false;
				else if (l < 1.5*dim) dPR = false;
				// forcer à 2
				else dPR = 2;
			} else {
				dPR = false;
			}


			var autorisees = this_img.attr("data-autorisees");					
			if (autorisees) {
				autorisees = $.parseJSON(autorisees.replace(/\\"/g, '"'));		
			}
							
			if (autorisees && autorisees[dim][mode]) {
				if (dPR < 1.5) url_img = autorisees[dim][mode][1];
				else url_img = autorisees[dim][mode][2];
			} else {
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
			}
			if (this_img.attr("data-background-actif") != url_img) {
				this_img.attr("data-background-actif", url_img);
				this_img.css("background-image", "url("+url_img+")");
			}
		}

}


function calculer_top_image_responsive(this_img) {
	this_img.attr("data-top", this_img.offset().top).addClass("lazy");
}

function charger_image_lazy(sTop) {
	if (typeof(sTop) == 'undefined') var sTop = $(window).scrollTop();
	var hauteur = $(window).height();
	
	var limite_haut = sTop - hauteur;
	if (limite_haut < 0) limite_haut = 0;
	
	var limite_bas = sTop + 1.5*hauteur;

	

		//console.log(sTop);

	$(".image_responsive.lazy[data-top]").each(function() {
		this_img = $(this);
		var h = this_img.attr("data-top");
		if (h <= limite_bas && h >= limite_haut) charger_url_image_responsive(this_img);
	});	
	$("[data-responsive=background].lazy[data-top]").each(function() {
		this_img = $(this);
		var h = this_img.attr("data-top");
		if (h <= limite_bas && h >= limite_haut) charger_url_background_responsive(this_img);
	});	
}

function charger_image_responsive () {
	// Calculer le "top" des images lazy
	$(".lazy, [data-lazy]").each(function() {
		calculer_top_image_responsive($(this));
	});
	
	// Remplacer les URL non lazy
	$(".image_responsive:not('.lazy'):not('.avec_picturefill')").each(function() {
		charger_url_image_responsive($(this));
	});
	charger_image_lazy();

	$("[data-responsive=background]:not('.lazy')").each(function() {
			charger_url_background_responsive($(this));
	});

}
var timeout_charger_image_responsive;
$(document).ready(function() {
	charger_image_responsive();
});
// Plus rattrapage:
$(document).on("ajaxComplete", function() {
	timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
});

$(window).on("load",function() {
	timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
});
$(window).smartresize(function() {
	timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
});
$(window).on("scroll touchmove", function() {
	charger_image_lazy();
});
	
