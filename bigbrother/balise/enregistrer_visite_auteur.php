<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_ENREGISTRER_VISITE_AUTEUR($p) {

	$i_boucle  = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$_id_objet = $p->boucles[$i_boucle]->primary;
	$_type     = $p->boucles[$i_boucle]->id_table;

    // Si c'est pas configuré pour, on arrête
    if (lire_config('bigbrother/enregistrer_visite_article') != 'oui')
    	return null;

    $obtenir = array(
		$_id_objet
	);

    return calculer_balise_dynamique(
    	$p,
    	'ENREGISTRER_VISITE_AUTEUR',$obtenir,
    	array("'$_type'")
    );

}

function balise_ENREGISTRER_VISITE_AUTEUR_stat($args, $context_compil) {
	spip_log($args,'test');
	spip_log($filtres,'test');
	// Pas d'id_article ? Erreur de squelette

	$_objet     = objet_type($context_compil[5]);

	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#ENREGISTRER_VISITE_AUTEUR',
					'motif' => 'ARTICLES')), '');

	return array($_objet,$args[0]);

}

function balise_ENREGISTRER_VISITE_AUTEUR_dyn($objet,$id_objet) {
	spip_log($objet.', '.$id_objet,'test');
	if (!($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])))
		return;

	find_in_path('bigbrother.php', 'inc/', true);

	// On enregistre l'entrée dans l'article
	$date_debut = bigbrother_enregistrer_entree($objet,$id_objet, $id_auteur);

	// On insère un script qui enregistrera la sortie
	echo '<script type="text/javascript">
	$(function(){
		$(window).unload(function(){
			$.get(
				"'.generer_url_public('bigbrother_enregistrer_sortie').'",
				{objet: '.$objet.', id_objet: '.$id_objet.', date_debut: \''.$date_debut.'\'}
			);
		});
	});
	</script>';
}

?>
