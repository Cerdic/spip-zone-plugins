
jQuery(document).ready(
  function() {
		jQuery(".cArticle div.formulaire_forum").hide();
		jQuery("#Layer0cmd").not(".open").click();
		jQuery("a#forum_repondre").click(function(e){
			e.preventDefault();
			jQuery("div#formulaire_forum:visible").slideUp("slow");
			jQuery("div#formulaire_forum:hidden").slideDown("slow");
		});
  }
);
