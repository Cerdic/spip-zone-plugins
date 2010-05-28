<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_ENREGISTRER_VISITE_AUTEUR($p) {

    // Si c'est pas configuré pour, on arrête
    if (lire_config('bigbrother/enregistrer_visite_article') != 'oui')
    	return null;

    return calculer_balise_dynamique(
    	$p,
    	'ENREGISTRER_VISITE_AUTEUR',
    	array(
    		'id_article'
    	)
    );

}

function balise_ENREGISTRER_VISITE_AUTEUR_stat($args, $filtres) {

    // Pas d'id_article ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#ENREGISTRER_VISITE_AUTEUR',
					'motif' => 'ARTICLES')), '');

	return $args;

}

function balise_ENREGISTRER_VISITE_AUTEUR_dyn($id_article) {

	if (!($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])))
		return;

	find_in_path('bigbrother.php', 'inc/', true);

	// On enregistre l'entrée dans l'article
	$date_debut = bigbrother_enregistrer_l_entree_d_un_article($id_article, $id_auteur);

	// On insère un script qui enregistrera la sortie
	echo '<script type="text/javascript">
	$(function(){
		$(window).unload(function(){
			$.get(
				"'.generer_url_public('bigbrother_enregistrer_la_sortie_d_un_article').'",
				{id_article: '.$id_article.', date_debut: \''.$date_debut.'\'}
			);
		});
	});
	</script>';


}

?>
