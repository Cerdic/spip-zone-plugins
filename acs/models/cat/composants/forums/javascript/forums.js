
jQuery(document).ready(
  function() {
		jQuery(".page_article div#formulaire_forum").hide();
		jQuery("#Layer0.forum_layer").attr("style","display:none");
		jQuery("a#forum_repondre").click(function(e){
			e.preventDefault();
			jQuery("div#formulaire_forum:visible").slideUp("slow");
			jQuery("div#formulaire_forum:hidden").slideDown("slow");
		});
  }
);