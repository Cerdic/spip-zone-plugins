
/* La fonction bloquerClick demande de confirmer la sortie de la page */
var afficher_bloquerClick;
function bloquerClick () {
	var ask = confirm("Attention, vous avez modifié le champ " + afficher_bloquerClick + "\rSouhaitez-vous continuer et perdre vos modifications?");
	if (!ask) return false;
}

/* Quand un champ de formulaire est change, on modifie le comportement des liens hypertexte de la page */
function surveiller_formulaires() {
	$("textarea,input,select").one("change", function(){
		var reg = new RegExp("<.*>|&nbsp;|\r|\n|\:", "g");
		afficher_bloquerClick = $(this).parent().find("label").html();
		
		if (!afficher_bloquerClick) afficher_bloquerClick = "Inconnu";
		else afficher_bloquerClick = afficher_bloquerClick.replace(reg,"")
		
		$("a[href^=http]").click(bloquerClick);
		$("a[onclick]").unbind("click", bloquerClick);

	});
	
	/* Quand un formulaire a ete valide, notamment en Ajax, desactiver le bloquage */
	$("input[type=submit]").click(function() {
		$("a[href^=http]").unbind("click", bloquerClick);	
	});
}

/* Activer au chargement */
$(document).ready(function(){
	surveiller_formulaires();
});

/* Activer quand on charge un element de page en Ajax */
$(document).ajaxComplete(surveiller_formulaires);
