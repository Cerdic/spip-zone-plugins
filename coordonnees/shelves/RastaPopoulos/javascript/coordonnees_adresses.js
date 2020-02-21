;(function($){
	/**
	 * Gérer les ensembles de champs d'adresses pour les remplacer suivant le pays
	 */
	function coordonnees_adresses_par_pays() {
		// On cherche tous les champs "pays" des adresses
		$('.editer_pays:has(select[data-adresse-id])').change(function() {
			var saisie_pays = $(this);
			var select = saisie_pays.find('select[data-adresse-id]');
			// Obligatoire ?
			var obligatoire = select.parents('.editer').is('.obligatoire') ? 'oui' : '';
			// On récupère l'identifiant de ce bloc d'adresse
			var identifiant = select.data('adresse-id');
			// Et le code pays demandé
			var code_pays = select.val();
			// API
			var api = '../' + 'adresses_par_pays.api/';
			// Environnement à garder pour le remplacement
			var env = ['adresse-id=' + identifiant, 'obligatoire=' + obligatoire];
			
			// On récupère les valeurs
			env.push(saisie_pays.siblings('.editer').find('[data-adresse-id=' + identifiant + ']').serialize());
			env = env.join('&');
			
			// On va cherche le HTML des nouveaux champs à remplacer
			$.get(api+code_pays, env, function(html) {
				// On supprime les anciens champs
				saisie_pays.siblings('.editer:has([data-adresse-id=' + identifiant + '])').remove();
				// Et on insère après la saisie pays
				saisie_pays.after(html);
			});
		});
	}
	
	// Après chargement de la page
	$(function() {
		coordonnees_adresses_par_pays();
		// Après rechargement d'un bloc ajax
		onAjaxLoad(coordonnees_adresses_par_pays);
	});
})(jQuery);
