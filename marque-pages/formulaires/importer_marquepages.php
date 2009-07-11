<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_importer_marquepages_charger_dist($id_rubrique){
	$valeurs = array();
	
	$valeurs['id_rubrique'] = $id_rubrique;
	$valeurs['fichier'] = '';
	
	// Si on a pas le droit, faut proposer le login
	include_spip('inc/autoriser');
	if (!autoriser('creermarquepagedans', 'rubrique', $id_rubrique)){
		
		$valeurs['message_erreur'] = _T('marquepages:pas_le_droit');
		
	}
	
	// preciser que le formulaire doit etre securise auteur/action
	$valeurs['_action'] = array('importer_marquepages', $id_rubrique);
	
	return $valeurs;
}

function formulaires_importer_marquepages_verifier_dist($id_rubrique){
	global $fichier_ok;
	$erreurs = array();
	
	if (!(sizeof($_FILES) > 0 && $_FILES['fichier']['size'] > 0)){
		$erreurs['message_erreur'] = _T('marquepages:erreur_importation');
	}
	else{
		$fichier_ok['chemin'] = $_FILES['fichier']['tmp_name'];
		
		// On récupère le contenu du fichier
		$contenu = file_get_contents($fichier_ok['chemin']);
		if (stripos($contenu, 'NETSCAPE-Bookmark-file') !== FALSE)
			$fichier_ok['type'] = 'netscape';
		elseif (stripos($contenu, '<post') !== FALSE){
			$fichier_ok['type'] = 'delicious';
		}
		else{
			$erreurs['message_erreur'] = _T('marquepages:erreur_type_inconnu');
		}
	}
	
	return $erreurs;
}

function formulaires_importer_marquepages_traiter_dist($id_rubrique){
	include_spip('inc/marquepages_api');
	include_spip('inc/invalideur');
	global $fichier_ok;
	$retours = array();
	
	$importer = 'marquepages_importer_'.$fichier_ok['type'];
	$retours = $importer($fichier_ok['chemin'], $id_rubrique);
	
	// Si tout s'est bien passé
	if (!$retours['message_erreur']){
		// On invalide tout le cache
		suivre_invalideur(1);
		// On redirige éventuellement
		if ($redirect = _request('redirect'))
			$retours['redirect'] = str_replace('&amp;', '&', $redirect);
	}
	
	return $retours;
}

?>
