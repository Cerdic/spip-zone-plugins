<?php

function formulaires_langonet_verifier_charger() {
	return array('verification' => _request('verification'),
				'fichier_langue' => _request('fichier_langue'),
				'dossier_scan' => _request('dossier_scan'));
}

function formulaires_langonet_verifier_verifier() {
	$erreurs = array();
	if (_request('fichier_langue') == '0') {
		$erreurs['fichier_langue'] = _T('langonet:message_choisir_langue');
	}
	if (_request('dossier_scan') == '0') {
		$erreurs['dossier_scan'] = _T('langonet:message_choisir_dossier');
	}
	return $erreurs;
}

function formulaires_langonet_verifier_traiter() {
	// Determination du type de verification et appel de la fonction idoine
	$verification = _request('verification');
	$langonet_verifier_items = charger_fonction('langonet_verifier_items','inc');

	// Recuperation des champs du formulaire
	//   $rep        -> nom du repertoire parent de lang/
	//                  'langonet' pour 'langonet/lang/'
	//                  correspond generalement au 'nom' du plugin
	//   $module     -> prefixe du fichier de langue
	//                  'langonet' pour 'langonet_fr.php'
	//                  parfois different du 'nom' du plugin
	//   $langue     -> index du nom de langue
	//                  'fr' pour 'langonet_fr.php'
	//   $ou_langue  -> chemin vers le fichier de langue a verifier
	//                  'plugins/auto/langonet/lang'
	//   $ou_fichier -> racine de l'arborescence a verifier
	//                  'plugins/auto/langonet'
	$retour_select_langue = explode(':', _request('fichier_langue'));
	$rep = $retour_select_langue[0];
	$module = $retour_select_langue[1];
	$langue = $retour_select_langue[2];
	$ou_langue = $retour_select_langue[3];
	$ou_fichier = _request('dossier_scan');

	// Les REGEXP de recherche de l'item de langue (voir le fichier regexp.txt)
	// pour les fichiers .html et .php
	define("_TROUVER_ITEM_HP", ",(?:<:|_[T|U]\(['\"])(?:([a-z0-9_]+):)?([a-z0-9_]+)((?:{(?:[^\|=>]*=[^\|>]*)})?(?:(?:\|[^>]*)?)(?:['\"]\s*\.\s*[^\s]+)?),iS");
	// pour les fichiers .xml
	define("_TROUVER_ITEM_X", ",<[a-z0-9_]+>[\n|\t|\s]*([a-z0-9_]+):([a-z0-9_]+)[\n|\t|\s]*</[a-z0-9_]+()>,iS");

	// Verification et formatage des resultats pour affichage
	$resultats = $langonet_verifier_items($rep, $module, $langue, $ou_langue, $ou_fichier, $verification);
	if (!$resultats['statut']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok'] = formater_resultats($resultats, $verification);
	}
	$retour['editable'] = true;
	return $retour;
}

function formater_resultats($resultats, $verification='definition') {
	$texte = '';
	if ($verification == 'definition') {
		// Liste des items non definis avec certitude
		if (count($resultats['item_non']) > 0) {
			if (count($resultats['item_non']) == 1) {
				$texte .= _T('langonet:message_ok_non_definis_1', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_non_definis_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			$texte .= afficher_lignes($resultats['fichier_non']);
			$texte .= "<br />\n";
		}
		else {
			$texte .= _T('langonet:message_ok_non_definis_0', array('module' => $resultats['module'], 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue']));
		}
		$texte .= "\n</p>\n<p class=\"reponse_formulaire reponse_formulaire_ok\">\n";
		// Liste des items definis sans certitude
		if (count($resultats['item_peut_etre']) > 0) {
			if (count($resultats['item_peut_etre']) == 1) {
				$texte .= _T('langonet:message_ok_definis_incertains_1', array('langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_definis_incertains_n', array('nberr' => count($resultats['item_peut_etre']), 'langue' => $resultats['langue'])) . "\n";
			}
			$texte .= afficher_lignes($resultats['fichier_peut_etre']);
		}
		else {
			$texte .= _T('langonet:message_ok_definis_incertains_0', array('module' => $resultats['module']));
		}
	}
	else {
		// Liste des items non utilises avec certitude
		if (count($resultats['item_non']) > 0) {
			if (count($resultats['item_non']) == 1) {
				$texte .= _T('langonet:message_ok_non_utilises_1', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_non_utilises_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			$texte .= "</p>\n";
			asort($resultats['item_non'], SORT_STRING);
			foreach($resultats['item_non'] as $_cle => $_item) {
				$texte .= "<div class=\"titrem\">" . $_item . "</div>\n";
			}
			$texte .= "<br />\n";
		}
		else {
			$texte .= _T('langonet:message_ok_non_utilises_0', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue']));
		}
		$texte .= "\n</p>\n";
		$texte .= "<p class=\"reponse_formulaire reponse_formulaire_ok\">\n";
		// Liste des items utilises sans certitude
		if (count($resultats['item_peut_etre']) > 0) {
			if (count($resultats['item_peut_etre']) == 1) {
				$texte .= _T('langonet:message_ok_utilises_incertains_1') . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_utilises_incertains_n', array('nberr' => count($resultats['item_peut_etre']))) . "\n";
			}
			$texte .= afficher_lignes($resultats['fichier_peut_etre']);
		}
		else {
			$texte .= _T('langonet:message_ok_utilises_incertains_0', array('module' => $resultats['module']));
		}
	}

	// generation du fichier texte des resultats
	$bak_nom = md5($verification{0}.$resultats['langue'].$resultats['ou_fichier']).'.txt';
	$bak_rep = sous_repertoire(_DIR_TMP, "langonet");
	$bak_fichier = $bak_rep . $bak_nom;
	$bak_texte = "langOnet : ";
	$bak_texte .= html_entity_decode(_T('langonet:bak_date_crea', array('bak_date_jour' => affdate(date('Y-m-d H:i:s')), 'bak_date_heure' => date('H:i:s'))))."\n\n";
	$bak_texte .= html_entity_decode(strip_tags($texte));
	$ok = ecrire_fichier($bak_fichier, $bak_texte);
	if (!$ok) {
		spip_log("echec de creation du fichier $bak_nom\n", "langonet", $bak_rep);
	}
	else {
		$texte .= "\n<br /></p>\n<p class=\"reponse_formulaire reponse_formulaire_ok\">\n";
		$texte .= _T('langonet:bak_info', array('bak_fichier' => $bak_rep.$bak_nom));
	}

	return $texte;
}

function afficher_lignes($tableau) {
	include_spip('inc/layer');
	// detail des fichiers utilisant les items de langue
	ksort($tableau);
	foreach ($tableau as $item => $detail) {
		$liste_lignes .= bouton_block_depliable($item, false);
		$liste_lignes .= debut_block_depliable(false);
		foreach ($tableau[$item] as $fichier => $ligne) {
			$liste_lignes .= "<p style=\"padding-left:2em;font-weight:bold;\">" .$fichier. "</p>\n";
			foreach ($tableau[$item][$fichier] as $ligne_n => $ligne_t) {
				$L = intval($ligne_n+1);
				$liste_lignes .= "<p style=\"padding-left:9em;text-indent: -5em;\">L.". sprintf("%04s", $L) .":<span style=\"padding-left:1em;\">".htmlentities($ligne_t[0]). "</span></p>\n";
			}
		}
		$liste_lignes .= fin_block();
	}
	
	return $liste_lignes;
}
?>