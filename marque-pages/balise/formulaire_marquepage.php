<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_MARQUEPAGE($p) {
	
    return calculer_balise_dynamique($p, 'FORMULAIRE_MARQUEPAGE', array('id_rubrique'));
    
}

function balise_FORMULAIRE_MARQUEPAGE_stat($args, $filtres) {
	
    // Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_MARQUEPAGE',
					'motif' => 'RUBRIQUES')), '');
	
	return $args;
	
}

function balise_FORMULAIRE_MARQUEPAGE_dyn($id_rubrique) {
	
	include_spip('inc/marquepages_api');
	
	// On récupère les éléments du formulaire
	$submit	= _request('mp_valider');
	$titre	= _request('mp_titre');
	$url 	= _request('mp_url');
	$desc 	= _request('mp_description');
	$tags 	= _request('mp_etiquettes');
	$retour	= rawurldecode(_request('retour'));
	
	// Si on a pas le droit, faut proposer le login
	if (!marquepages_autoriser_creer($id_rubrique)){
		
		$proposer_login = true;
		$message_erreur = _T('marquepages:pas_le_droit');
		
	}
	else $proposer_login = false;
	
	// Si le formulaire a été rempli
	if ($submit) {
		
		// Si tout va, on intègre à la base de données
		if (($message_erreur = marquepages_test_parametres($url, $titre, $desc, $tags, $id_rubrique)) == ''){
			
			$ok = marquepages_ajouter($url, $titre, $desc, $tags, $id_rubrique);
			if ($ok != 0) $message_ok = _T('marquepages:enregistre');
			else $message_erreur = "Error with the database.";
			
		}
	
	}
	
	// On provoque enfin l'affichage
    return array(
        'formulaires/marquepage', 
        0, 
        array(
        	'self' => 	parametre_url(
							parametre_url(
								parametre_url(
									parametre_url(
										str_replace('&amp;', '&', self())
									,'mp_url', '')
								, 'mp_titre', '')
							, 'mp_description', '')
						, 'mp_etiquettes', ''),
        	'retour' => str_replace('&amp;', '&', $retour),
        	'message_ok' => $message_ok,
			'message_erreur' => $message_erreur,
			'proposer_login' => $proposer_login, 
			'mp_titre' => $titre,
			'mp_url' => $url,
			'mp_description' => $desc,
			'mp_etiquettes' => $tags			
        )
    );
    
}

?>
