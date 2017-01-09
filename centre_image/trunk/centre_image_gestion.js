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
			start: function(event, ui) {
				var liste_documents = ui.helper.parents('.liste_items.documents.ui-sortable');
				if (liste_documents.length) {
					liste_documents.sortable('cancel');
					liste_documents.sortable('disable');
				}
			},
			stop: function(event, ui) {
				var liste_documents = ui.helper.parents('.liste_items.documents.ui-sortable');
				if (liste_documents.length) {
					liste_documents.sortable('enable');
				}

				var lien = el.attr("href");
				var url = lien.replace(/^\.\.\//, '')

				var x = ui.position.left / el.find("img.img_source").width();
				var y = ui.position.top / el.find("img.img_source").height();

				x = Math.max(0, x);
				x = Math.min(1, x);
				y = Math.max(0, y);
				y = Math.min(1, y);

				$.ajax("index.php?action=centre_image_forcer&x="+x+"&y="+y+"&url="+url);
			}
		});
}

function centre_image_afficher() {

	$(".portfolios, .formulaire_editer_logo .spip_logo, #documents_joints").find("a[href$=jpg].hasbox, a[href$=png].hasbox, a[href$=gif].hasbox").each(function(){

		// recuperer l'URL sans les ../
		var lien = $(this).attr("href");
		var url = lien.replace(/^\.\.\//, '')

		if ($(this).parents(".spip_documents").length == 0) {
			$(this).attr("data-href", url);
		}

		$.getJSON( "../index.php?page=centre_image_json&url="+url, 
			{lien: lien}, 
			function( data ) {
				var el = $("a[data-href='"+url+"']");
				var x = data.x * el.find("img:not(.croix_centre_image)").width();
				var y = data.y * el.find("img:not(.croix_centre_image)").height();
				// console.log(url + " / " + x + " / " + y);
				centre_image_croix(el, x, y);
			}
		);

	});
}


(function($){
	$(document).ready(centre_image_afficher);
	onAjaxLoad(centre_image_afficher);
})(jQuery);
