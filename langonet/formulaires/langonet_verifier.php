<?php

function formulaires_langonet_verifier_charger(){
	$valeurs = array();
	$champs = array('module', 'langue', 'chemin_fichier', 'chemin_langue');
	foreach($champs as $_champ){
		$valeurs[$_champ] = _request($_champ);
	}
	return $valeurs;
}

function formulaires_langonet_verifier_verifier(){
	$erreurs = array();
	$obligatoires = array('module', 'langue', 'chemin_fichier', 'chemin_langue');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_langonet_verifier_traiter(){
	// Determination du type de verification et appel de la fonction idoine
	$verification = _request('verification');
	if ($verification == 'definition')
		$langonet_verifier_langue = charger_fonction('langonet_verifier_definition','inc');
	else
		$langonet_verifier_langue = charger_fonction('langonet_verifier_utilisation','inc');
	// Recuperation des champs du formulaire
	$champs = array('module', 'langue', 'chemin_fichier', 'chemin_langue');
	foreach($champs as $_champ){
		$champs[$_champ] = _request($_champ);
	}
	// Verification et formatage des resultats pour l'affichage dans le formulaire
	$resultats = $langonet_verifier_langue($champs['module'], $champs['langue'], $champs['chemin_langue'], $champs['chemin_fichier']);
	if (!$resultats['statut']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok'] = formater_resultats($resultats, $verification);
	}
	$retour['editable'] = true;
	return $retour;
}

function formater_resultats($resultats, $verification='definition'){
	$texte = '';
	if ($verification == 'definition') {
		// Liste des items non definis avec certitude
		if (count($resultats['non_definis']) > 0) {
			$texte .= _T('langonet:message_ok_n_non_definis', array(nberr => count($resultats['non_definis']))) . '<br /><br />';
			foreach($resultats['non_definis'] as $_cle => $_item) {
				$texte .= '&raquo; ' . $_item . '<br />';
			}
		}
		else
			$texte .= _T('langonet:message_ok_0_non_defini');
		$texte .= '<br /><br />';
		// Liste des items definis sans certitude
		if (count($resultats['a_priori_definis']) > 0) {
			$texte .= _T('langonet:message_ok_n_definis_incertains', array(nberr => count($resultats['a_priori_definis']))) . '<br /><br />';
			foreach($resultats['a_priori_definis'] as $_cle => $_item) {
				$texte .= '&raquo; ' . $_item . '<br />';
			}
		}
		else
			$texte .= _T('langonet:message_ok_0_defini_incertain');
	}
	else {
		// Liste des items non utilises avec certitude
		if (count($resultats['non_utilises']) > 0) {
			$texte .= _T('langonet:message_ok_n_non_utilises', array(nberr => count($resultats['non_utilises']))) . '<br /><br />';
			foreach($resultats['non_utilises'] as $_cle => $_item) {
				$texte .= '&raquo; ' . $_item . '<br />';
			}
		}
		else
			$texte .= _T('langonet:message_ok_0_non_utilise');
		$texte .= '<br /><br />';
		// Liste des items utilises sans certitude
		if (count($resultats['a_priori_utilises']) > 0) {
			$texte .= _T('langonet:message_ok_n_utilises_incertains', array(nberr => count($resultats['a_priori_utilises']))) . '<br /><br />';
			foreach($resultats['a_priori_utilises'] as $_cle => $_item) {
				$texte .= '&raquo; ' . $_item . '<br />';
			}
		}
		else
			$texte .= _T('langonet:message_ok_0_utilise_incertain');
	}
	$texte .= '<br /><br />';

	return $texte;
}

?>