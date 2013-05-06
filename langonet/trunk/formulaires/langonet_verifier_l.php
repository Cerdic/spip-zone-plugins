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

	// Pour la vérification de la fonction _L(), on ne choisit pas de fichier de langue. En outre,
	// à partir du moment où plusieurs arborescences sont scannées il n'est plus possible de construire
	// un seul fichier de corrections. Aussi, plutôt que d'en construire n, on construit un seul fichier
	// contenant l'ensemble des items créés.
	// Néanmoins, pour créer le fichier de langue corrigé en rajoutant les nouveaux items devant remplacer
	// les appels à _L() il est nécessaire d'en choisir un.
	// Aussi, on choisit la langue de référence pour le module
	$langue = 'fr';
	$module = '';
	$ou_langue = '';

	// Creation du fichier de langue corrigé avec les items detectes comme
	// non definis ou obsoletes suivant la verification en cours.
	// Pour la vérification de la fonction _L(), il est possible de corriger plusieurs fichiers correspondant
	// à plusieurs arborescences de plugins.
	$items_a_corriger = $resultats["item_non"];
	if ($items_a_corriger) {
		$encodage = 'utf8';
		$mode = 'fonction_l';

		$extra = array();
		foreach ($items_a_corriger as $_item) {
			$index = preg_match('/^(.*)[{].*[}]$/', $_item, $m) ? $m[1] : $_item;
			$extra[$index] = @$resultats['item_non'][$_item];
		}

		$langonet_corriger = charger_fonction('langonet_generer_fichier','inc');

		// Pour la vérification de la fonction _L(), on regarde si plusieurs plugins ont été corrigés et
		// on crée des listes séparées d'items à corriger.

//		$corrections = $langonet_corriger($module, $langue, $ou_langue, $langue, $mode, $encodage, $extra);
	}

	// Traitement des resultats
	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_verification');
		$retour['message_ok']['resultats'] = $resultats;
	}
	$retour['editable'] = true;
	return $retour;
}

?>