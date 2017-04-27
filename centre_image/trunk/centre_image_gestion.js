function centre_image_croix(el, x, y) {
	if (el.find("img.croix_centre_image").length == 0) {
		el.css("display", "inline-block").css("position", "relative")
			.find("img").addClass("img_source").css("border", "1px solid green")
			.parent()
			.prepend("<img src='"+croix+"' class='croix_centre_image' style='cursor: move; position: absolute; margin-left: -7px; margin-top: -7px; margin-right: -7px; margin-bottom: -7px;background:transparent;'>");
	}

	el.find("img.croix_centre_image").css("left", x+"px").css("top", y+"px")
		.draggable({
			containment: "parent",
			stop: function(event, ui) {
				var lien = el.attr("href");
				var url = lien.replace(/^\.\.\//, '');
				url = url.split('?').shift();

				var x = ui.position.left / el.find("img.img_source").width();
				var y = ui.position.top / el.find("img.img_source").height();

				x = Math.max(0, x);
				x = Math.min(1, x);
				y = Math.max(0, y);
				y = Math.min(1, y);

				el.data('x', x);
				el.data('y', y);

				$.ajax("index.php?action=centre_image_forcer&x="+x+"&y="+y+"&url="+url);
			}
		});
}

function centre_image_calculer_croix(el) {
	var x = el.data('x');
	var y = el.data('y');
	x = x * el.find("img:not(.croix_centre_image)").width();
	y = y * el.find("img:not(.croix_centre_image)").height();
	// console.log(url + " / " + x + " / " + y);
	centre_image_croix(el, x, y);
}

jQuery.fn.centre_images = function() {
	var images = $(this).find("a[href$=jpg].hasbox, a[href$=png].hasbox, a[href$=gif].hasbox, a[type='image/jpeg'].hasbox, a[type='image/png'].hasbox, a[type='image/gif'].hasbox");
	images.each(function () {
		// recuperer l'URL sans les ../
		var lien = $(this).attr("href");
		var url = lien.replace(/^\.\.\//, '');
		url = url.split('?').shift();

		if ($(this).parents(".spip_documents").length == 0) {
			$(this).attr("data-href", url);
		}

		$.getJSON("../index.php?page=centre_image_json&url=" + url,
			function (data) {
				var el = $("a[data-href='" + url + "']");
				el.data('x', data.x);
				el.data('y', data.y);
				centre_image_calculer_croix(el);
			}
		);
	});
}

jQuery.fn.centre_images_rafraichir = function() {
	var images = $(this).find("a[href$=jpg].hasbox, a[href$=png].hasbox, a[href$=gif].hasbox, a[type='image/jpeg'].hasbox, a[type='image/png'].hasbox, a[type='image/gif'].hasbox");
	images.each(function () {
		centre_image_calculer_croix($(this));
	});
}

function centre_image_afficher() {
	$(".portfolios, .formulaire_editer_logo .spip_logo, #documents_joints, .documents-album").centre_images();
	$(".portfolios .liste_items.documents").on("affichage.documents.change", function(){
		$(this).centre_images_rafraichir();
	});
}


(function($){
	$(document).ready(centre_image_afficher);
	onAjaxLoad(centre_image_afficher);
})(jQuery);
