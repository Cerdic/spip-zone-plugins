jQuery(function(){
	(function ($) {
	$("div#formulaire_recommander").hide().css("height","");
	$("#recommander>h2, #recommander_bouton").click(function(){
		$("div#formulaire_recommander:visible").slideUp("slow");
		$("div#formulaire_recommander:hidden").slideDown("slow");
		return false; // si jamais le bouton est un lien
	});
	}(jQuery));
});