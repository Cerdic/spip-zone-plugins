/*
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 */

$(document).ready(function(){
	var image_affichee = "", cache_cree = false;
	$("#affichage").load(function() { 
		var current_image = $(this).attr("src");
		if(image_affichee != current_image) {
			image_affichee = current_image;
			$(this).fadeIn("slow",function(){
				if(!cache_cree) {
					cache_cree = true;
					$(this).after("<img id='affichage_cache' src='' />");
					$("#affichage_cache").attr("style", $(this).css("style"));
					$("#affichage_cache").css("z-index", $(this).css("z-index") - 1);
				}
				$("#affichage_cache").attr("src", $(this).attr("src"));
				$("#affichage_cache").fadeOut("slow");
			});
		}
	});
});