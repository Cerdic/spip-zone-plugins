<?php

function formulaires_plugonet_generer_charger() {
	$valeurs = array();
	return $valeurs;
}

function formulaires_plugonet_generer_verifier() {
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach ($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_generer_traiter() {
	// Recuperation des champs du formulaire
	$pluginxml = _request('pluginxml');
	$forcer = (_request('forcer')) ? true : false;

	// Generation du fichier
 	$generer = charger_fonction('plugonet_generer','inc');
 	list($erreurs, $commandes) = $generer($pluginxml, $forcer);

	// Formatage et affichage des resultats
	var_dump('erreurs', $erreurs);
	var_dump('commandes', $commandes);
	// -- Message global sur la generation des fichiers
	$retour = array();
 	list($ok, $message) = formater_message($erreurs);
 	if ($ok)
	 	$retour['message_ok']['resume'] = $message;
	else
	 	$retour['message_erreur'] = $message;
	// -- Texte des resultats par fichier traite
// 	$retour['message_ok']['resultats'] = formater_resultats($erreurs, $commandes);

	$retour['editable'] = true;
	
	return $retour;
}

function formater_message($erreurs) {
	// Nombre de fichiers traites
	$nb_fichiers = count($erreurs);	

	// Determiniation des compteurs d'erreurs et de notices
	$nb_errlec = $nb_errinf = $nb_errval = $nb_notval = 0;
	foreach ($erreurs as $_pluginxml => $_erreurs) {
		// Erreur : chaque type est exclusif
		$nb_errlec = ($_erreurs['erreur_lecture_pluginxml']) ? $nb_errlec + 1 : $nb_errlec;
		$nb_errinf = ($_erreurs['erreur_information_pluginxml']) ? $nb_errinf + 1 : $nb_errinf;
		$nb_errval = (count($_erreurs['erreur_validation_paquetxml']) > 0) ? $nb_errval + 1 : $nb_errval;
		// Notice : pseudo-validation du plugin.xml
		$nb_notval = (count($_erreurs['notice_validation_pluginxml']) > 0) ? $nb_notval + 1 : $nb_notval;
	}
	
	if (($nb_errlec + $nb_errinf + $nb_errval) > 0) {
		$ok = false;
		$message = _T('plugonet:message_nok_generation_paquetxml');
	}
	else {
		$ok = true;
		$message = _T('plugonet:message_ok_generation_paquetxml');
	}

	return array($ok, $message);
}

?>