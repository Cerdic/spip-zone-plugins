function charger_image_responsive () {
	$(".image_responsive").each(function() {
		var src = $(this).attr("data-src");
		var w= parseInt($(this).width());
		if(dPR = window.devicePixelRatio) {
			w = parseInt(w*dPR);
		}
		
		if (htactif) {
			racine = src.substr(0, src.length-4);
			terminaison = src.substr(src.length-3, 3);
			$(this).attr("src", racine+"-resp"+w+"."+terminaison);
		} else {
			$(this).attr("src", "index.php?action=image_responsive&img="+src+"&taille="+w);
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