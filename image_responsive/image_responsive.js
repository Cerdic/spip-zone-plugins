function charger_image_responsive () {
	var dPR = window.devicePixelRatio;

	$(".image_responsive").each(function() {
		var this_img = $(this);
		var src = this_img.attr("data-src");
		var w= parseInt(this_img.width());
		
		
		if (w == 0) {
		
		} else {
		
			if(dPR) {
				w = parseInt(w*dPR);
			}
			
			if (htactif) {
				racine = src.substr(0, src.length-4);
				terminaison = src.substr(src.length-3, 3);
				var url_img = racine+"-resp"+w+"."+terminaison;
			} else {
				var url_img = "index.php?action=image_responsive&img="+src+"&taille="+w;
			}
			
			this_img.attr("src", url_img);
		}

	});

}
var timeout_charger_image_responsive;
$(document).ready(function() {
	charger_image_responsive();
	
	$(window).on("resize",function() {
		timeout_charger_image_responsive = setTimeout("charger_image_responsive()",200);
	});
});