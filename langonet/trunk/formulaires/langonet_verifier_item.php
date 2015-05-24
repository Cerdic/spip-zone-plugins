<?php

function formulaires_langonet_verifier_item_charger() {

	return array('verification' => _request('verification'),
				'fichier_langue' => _request('fichier_langue'),
				'version' => _request('version'),
				'dossier_scan' => sinon(_request('dossier_scan'),array()));
}

function formulaires_langonet_verifier_item_verifier() {
	$erreurs = array();
	if (_request('fichier_langue') == '0') {
			$erreurs['fichier_langue'] = _T('langonet:message_nok_champ_obligatoire');
	}
	if (!is_array(_request('dossier_scan')) OR count(_request('dossier_scan')) == '0') {
		$erreurs['dossier_scan'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}


function formulaires_langonet_verifier_item_traiter() {

	$retour = array();

	// Recuperation des champs du formulaire communs à toutes les vérifications
	//  $verification -> type de verification 'definition' ou 'utilisation'
	//  $ou_fichiers   -> tableau des racines d'arborescences à verifier 'plugins/auto/langonet'
	//  $module       -> prefixe du fichier de langue : 'langonet' pour 'langonet_fr.php'
	//                   parfois different du 'nom' du plugin
	//  $langue       -> index du nom de langue, 'fr' pour 'langonet_fr.php'
	//  $ou_langue    -> chemin vers le fichier de langue a verifier 'plugins/auto/langonet/lang'
	$verification = _request('verification');
	$ou_fichiers = _request('dossier_scan');
	$retour_select_langue = explode(':', _request('fichier_langue'));
	$module = $retour_select_langue[1];
	$langue = $retour_select_langue[2];
	$ou_langue = $retour_select_langue[3];

	// Lancement de la vérification utilisation ou définition
	$langonet_verifier_items = charger_fonction('verifier_items','inc');
	$resultats = $langonet_verifier_items($module, $langue, $ou_langue, $ou_fichiers, $verification);

	// Creation du fichier de langue corrigé avec les items detectes comme
	// non definis ou obsoletes suivant la verification en cours.
	$items_a_corriger = $resultats['occurrences_non'];
	if ($items_a_corriger) {
		$encodage = 'utf8';
		$mode = ($verification == 'definition') ? 'oublie' : 'inutile';

		$langonet_corriger = charger_fonction('generer_fichier','inc');
		$corrections = $langonet_corriger($module, $langue, $ou_langue, $langue, $mode, $encodage, $items_a_corriger);
		if ($corrections['fichier']) {
			$retour['message_ok']['corrections']['fichier'] = $corrections['fichier'];
			$retour['message_ok']['corrections']['explication'] = _T("langonet:message_ok_corrections_${verification}",
														array('fichier' => $corrections['fichier']));
		}
		else
			$retour['message_ok']['corrections']['explication'] = _T('langonet:message_nok_corrections');
	}

	// Traitement des resultats
	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		// Résultats communs aux deux vérifications
		$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_verification');
		$retour['message_ok']['resultats']['module'] = $resultats['module'];
		$retour['message_ok']['resultats']['ou_fichier'] = $resultats['ou_fichier'];
		$retour['message_ok']['resultats']['occurrences_non'] = $resultats['occurrences_non'];
		$retour['message_ok']['resultats']['occurrences_non_mais'] = $resultats['occurrences_non_mais'];
		$retour['message_ok']['resultats']['occurrences_peut_etre'] = $resultats['occurrences_peut_etre'];
		// Uniquement pour la vérification des définitions
		$retour['message_ok']['resultats']['occurrences_oui_mais'] = isset($resultats['occurrences_oui_mais']) ? $resultats['occurrences_oui_mais'] : array();
		$retour['message_ok']['resultats']['complements'] = isset($resultats['complements']) ? $resultats['complements'] : array();
	}
	$retour['editable'] = true;
	return $retour;
}

?>