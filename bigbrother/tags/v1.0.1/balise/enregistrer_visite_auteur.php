<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/config');

function balise_ENREGISTRER_VISITE_AUTEUR($p) {
	$i_boucle  = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$_id_objet = $p->boucles[$i_boucle]->primary;

    // Si c'est pas configuré pour, on arrête
    if (lire_config('bigbrother/visite_entree') != 'oui')
    	return null;

    if($i_boucle)
	    return calculer_balise_dynamique(
	    	$p,
	    	'ENREGISTRER_VISITE_AUTEUR',array(
	    		'ENREGISTRER_VISITE_TYPE_BOUCLE',
				$_id_objet
			)
	    );
}

function balise_ENREGISTRER_VISITE_AUTEUR_stat($args, $context_compil) {
	// Pas d'id_objet ? Erreur de squelette
	if (!$args[0] OR !$args[1]){
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#ENREGISTRER_VISITE_AUTEUR',
					'motif' => 'ARTICLES')), '');
	}
	return array($args[0],$args[1]);

}

function balise_ENREGISTRER_VISITE_AUTEUR_dyn($objet,$id_objet) {
	if ((!intval($GLOBALS['visiteur_session']['id_auteur'])) && (lire_config('bigbrother/enregistrer_connexion_anonyme') != 'oui'))
		return;

	$objet = objet_type($objet);
	$id_auteur = intval($GLOBALS['visiteur_session']['id_auteur']) ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip'];

	// On enregistre l'entrée dans l'article
	$date_debut = bigbrother_enregistrer_entree($objet,$id_objet, $id_auteur);

	// On insère un script qui enregistrera la sortie
	echo '<script type="text/javascript">
	$(function(){
		$(window).unload(function(){
			jQuery.ajax({
				async:false,
				cache: false,
        		dataType: "script",
				type: "GET",
				url: "'.generer_url_public('bigbrother_enregistrer_sortie').'",
				data: {objet: \''.$objet.'\', id_objet: '.$id_objet.', date_debut: \''.$date_debut.'\'}
			});
		});
	});
	</script>';
}

function balise_ENREGISTRER_VISITE_TYPE_BOUCLE($p){
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? $type : '';
	return $p;
}
?>
