<?php



// Formulaire pour composer la feuille de route

// Chargement des valeurs
function formulaires_editer_feuillederoute_charger_dist(){
	$valeurs = array( '_texte' => '' );
	lire_fichier_securise(_DIR_IMG . 'feuillederoute.php',$contenu);
	$contenu = @unserialize($contenu);
	if($contenu){
		$valeurs['_texte'] = $contenu;
	}
	return $valeurs;
}

// Vérification des valeurs du formulaire
function formulaires_editer_feuillederoute_verifier_dist(){
	$erreurs = array();    
	return $erreurs;
}

// Traitement des valeurs du formulaire
function formulaires_editer_feuillederoute_traiter_dist(){
	$contenu = _request('texte');
	ecrire_fichier_securise(_DIR_IMG . 'feuillederoute.php', serialize($contenu));
	return array('message_ok' => _T('feuillederoute:message_ok'));
}

?>