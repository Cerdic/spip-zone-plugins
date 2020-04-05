// Fonction ayant pour but de lancer en ajax la mise à jour des plugins
// pour l'ensemble des sites mutualisés.
// @author      RealET

function plugins_upgrade() {
	var ident;
	$("#pluginsupgrade").attr('disabled', 'disabled');
	$.each(tableau_upgrade,function(num_site,site){
		ident="#upgrade"+(num_site+1);
		$(ident).empty().append('&nbsp;&nbsp;&nbsp;');
		$.get(site,{up: ident}, function(id) {
			affiche_resultat(id, 'Fait&nbsp;!');
		});
	});
}

function affiche_resultat(ident, resultat) {
	$(ident).removeClass("loading");
	$(ident).empty().append(resultat);
}