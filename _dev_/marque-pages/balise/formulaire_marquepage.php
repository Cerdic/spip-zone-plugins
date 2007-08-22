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
	
	// Si on a pas le droit, faut proposer le login
	if (!marquepages_autoriser_creer($id_rubrique)){
		
		$proposer_login = true;
		$message_erreur = marquepages_test_parametres($url, $titre, $desc, $tags, $id_rubrique);
		
	}
	else $proposer_login = false;
	
	// Si le formulaire a été rempli
	if ($submit) {
		
		// Si tout va, on intègre à la base de données
		if (($message_erreur = marquepages_test_parametres($url, $titre, $desc, $tags, $id_rubrique)) == ''){
			
			marquepages_ajouter($url, $titre, $desc, $tags, $id_rubrique);
			$message_ok = _T('marquepages:enregistre');
			
		}
	
	}
	
	// On provoque enfin l'affichage
    return array(
        'formulaires/marquepage', 
        0, 
        array(
        	'self' => str_replace('&amp;', '&', self()),
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
