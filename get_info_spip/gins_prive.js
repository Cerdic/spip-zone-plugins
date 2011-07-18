
/*
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
*/

(function($) {
	
	// attendre document chargé 
	$(document).ready(function() {
	
		// commencer par tout replier
		$("#gins-contenu div.gins-item").hide();
		
		// afficher le premier
		$("#gins-contenu div.gins-item:first").show();
		
		/**
		 * Animation sur les onglets
		 */
		var menu_nb_items = $("#gins-menu li").length;
		var menu_width = $("#gins-menu").width();
		
		var menu_item_width_max = 0;
		$("#gins-menu li").each(function() {
			menu_item_width_max = Math.max(menu_item_width_max, $(this).width());
		});
		menu_item_width_max += 8;
		
		var menu_item_width_min = Math.ceil(
					(menu_width - menu_item_width_max - (32 * menu_nb_items))
					/ menu_nb_items);
		
		$("#gins-menu h3").each(function(index) {
			if ($(this).hasClass("on"))
			{
				$(this).css({
					width: menu_item_width_max+"px"
				});
			}
			else
			{
				$(this).css({
					width: menu_item_width_min+"px",
					overflow: "hidden"
				});
			}
		});
		
		onglet_actif = $("#gins-menu li.on h3");

		$("#gins-menu li h3").hover(function() {
			$(onglet_actif).animate({
				width: menu_item_width_min+"px"
				}, {
					queue:false,
					duration:400
					});
			$(this).animate({
				width: menu_item_width_max+"px"
				}, {
					queue:false,
					duration:400
					});
			onglet_actif = this;
		});
		
		$("#gins-menu li h3").mouseout(function() {
			$("#gins-menu li.off h3").animate({
				width: menu_item_width_min+"px"
				}, {
					queue:false,
					duration:400
					});
			$("#gins-menu li.on h3").animate({
				width: menu_item_width_max+"px"
				}, {
					queue:false,
					duration:400
					});
			onglet_actif = $("#gins-menu li.on h3");
		});
		
		/**
		 * Les boutons/onglets affichent
		 * le bloc info
		 */
		$("#gins-menu a").click(function () {
			$("#gins-menu li").removeClass("on").addClass("off");
			$(this).closest("li").removeClass("off").addClass("on");
			onglet_actif = $("#gins-menu li.on h3");
			$("#gins-contenu div.gins-item").hide();
			$("#gins-"+$(this).attr("name")).show();
		}); 

	});
	
})(jQuery);
