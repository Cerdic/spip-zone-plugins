function charger_image_responsive () {
	var dPR = window.devicePixelRatio;

	$(".image_responsive").each(function() {
		var this_img = $(this);
		var src = this_img.attr("data-src");
		var l = this_img.attr("data-l");
		var h = this_img.attr("data-h");
		var appliquer_dPR = this_img.attr("data-dpr");
		var w= parseInt(this_img.width());
		
		
		// Si l'image est trop petite, c'est pas la peine de demander trop grand…
		if (w > l) {
			w = l;
			dpr = false;
		}
		
		
		if (w == 0) {
		
		} else {
		
			if(dPR && dPR > 1 && appliquer_dPR != 0) {
				
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
			this_img.attr("src", url_img);
		}

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

$(window).on("resize load",function() {
	timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
});
