<?php

function formulaires_langonet_verifier_l_charger() {
	return array('dossier_scan' => sinon(_request('dossier_scan'), '0'));
}

function formulaires_langonet_verifier_l_verifier() {
	$erreurs = array();
	if (!_request('dossier_scan')) {
		$erreurs['dossier_scan'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}

function formulaires_langonet_verifier_l_traiter() {

	$retour = array();

	// Recuperation des champs du formulaire communs à toutes les vérifications
	//  $verification -> type de verification 'definition' ou 'utilisation'
	//  $ou_fichier   -> racine de l'arborescence a verifier 'plugins/auto/langonet'
	$ou_fichier = _request('dossier_scan');

	// Lancement de la vérification fonction_l
	$langonet_verifier_items = charger_fonction('langonet_verifier_l','inc');
	$resultats = $langonet_verifier_items($ou_fichier);

	// Creation du fichier de langue corrigé avec les items construits pour chaque cas d'utilisation de
	// la fonction _L().
	if ($resultats['total_occurrences'] > 0) {
		$encodage = 'utf8';
		$mode = 'fonction_l';

		// Pour la vérification de la fonction _L(), on ne choisit pas de fichier de langue dans le formulaire.
		// Néanmoins, il est nécessaire d'en définir un pour créer le fichier de langue corrigé.
		// On cherche donc un répertoire lang/ dans lequel il existe des fichiers de langue et on essaye de
		// déterminer le module à corriger ainsi que la langue de référence.
		// Si aucun module n'est trouvé on choisit le module "indefini" et la langue de référence "fr".
		include_spip('inc/langonet_utils');
		list($module, $langue, $ou_langue) = trouver_module_langue($ou_fichier);

		$langonet_corriger = charger_fonction('langonet_generer_fichier','inc');
		$corrections = $langonet_corriger($module, $langue, $ou_langue, $langue, $mode, $encodage, $resultats["items_a_corriger"]);
		if ($corrections['fichier']) {
			$retour['message_ok']['corrections']['fichier'] = $corrections['fichier'];
			$retour['message_ok']['corrections']['explication'] = _T('langonet:message_ok_corrections_fonction_l',
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
		$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_verification');
		$retour['message_ok']['resultats']['ou_fichier'] = $resultats['ou_fichier'];
		$retour['message_ok']['resultats']['total_occurrences'] = $resultats['total_occurrences'];
		$retour['message_ok']['resultats']['occurrences_non'] = $resultats['occurrences_non'];
	}
	$retour['editable'] = true;

	return $retour;
}

?>