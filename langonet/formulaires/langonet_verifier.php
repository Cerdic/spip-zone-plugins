<?php

function formulaires_langonet_verifier_charger() {
	return array('verification' => _request('verification'),
				'fichier_langue' => _request('fichier_langue'),
				'dossier_scan' => _request('dossier_scan'));
}

function formulaires_langonet_verifier_verifier() {
	$erreurs = array();
	if (_request('fichier_langue') == '0') {
		$erreurs['fichier_langue'] = _T('langonet:message_nok_champ_obligatoire');
	}
	if (_request('dossier_scan') == '0') {
		$erreurs['dossier_scan'] = _T('langonet:message_nok_champ_obligatoire');
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

	// Verification et formatage des resultats pour affichage
	$retour = array();
	$resultats = $langonet_verifier_items($rep, $module, $langue, $ou_langue, $ou_fichier, $verification);
	if ($resultats['erreur']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour = formater_resultats($verification, $resultats);
	}
	$retour['editable'] = true;
	return $retour;
}

/**
 * Formatage des resultats pour affichage dans le formulaire
 *
 * @param string $verification
 * @param string $resultats
 * @return array
 */

// $resultats    => tableau des resultats (11 sous-tableaux) :
//                    ["module"] => intitule module
//                    ["ou_fichier"] => rep plugin
//                    ["langue"] => nom fichier de lang
//                    ["item_non"][] => intitule item
//                    ["fichier_non"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["item_non_mais"][] => intitule item
//                    ["fichier_non_mais"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["item_peut_etre"][] => intitule partiel item
//                    ["fichier_peut_etre"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["definition_possible"][item][] => nom fichier de lang
//                    ["definition_ok"] => 
// $verification => type de verification effectuee (definition ou utilisation)
function formater_resultats($verification, $resultats) {

	// On initialise le tableau des textes resultant contenant les index:
	// - ["message_ok"]["resume"] : le message de retour ok fournissant le fichier des resultats
	// - ["message_ok"]["resultats"] : le texte des résultats
	// - ["message_erreur"] : le message d'erreur si on a erreur de traitement pendant l'execution
	$retour = array();

	$texte = array('non' => '', 'non_mais' => '', 'peut_etre' => '');
	if ($verification == 'definition') {
		// Liste des items non definis avec certitude et bien utilises avec le bon module
		if (count($resultats['item_non']) > 0) {
			$texte['non'] .= '<div class="error">'  . "\n";
			if (count($resultats['item_non']) == 1) {
				$texte['non'] .= _T('langonet:message_ok_non_definis_1', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte['non'] .= _T('langonet:message_ok_non_definis_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			$texte['non'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['non'] .= afficher_lignes($resultats['fichier_non']);
			$texte['non'] .= "</div>\n</div>\n";
		}
		else {
			$texte['non'] .= '<div class="success">' . "\n";
			$texte['non'] .= _T('langonet:message_ok_non_definis_0', array('module' => $resultats['module'], 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			$texte['non'] .= "</div>\n";
		}

		// Liste des items non definis mais utilises avec un module different de celui 
		// en cours de verification
		if (count($resultats['item_non_mais']) > 0) {
			$texte['non_mais'] .= '<div class="notice">' . "\n";
			if (count($resultats['item_non_mais']) == 1) {
				$texte['non_mais'] .= _T('langonet:message_ok_nonmais_definis_1', array('ou_fichier' => $resultats['ou_fichier'], 'module' => $resultats['module'])) . "\n";
			}
			else {
				$texte['non_mais'] .= _T('langonet:message_ok_nonmais_definis_n', array('nberr' => count($resultats['item_non_mais']), 'ou_fichier' => $resultats['ou_fichier'], 'module' => $resultats['module'])) . "\n";
			}
			$texte['non_mais'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['non_mais'] .= afficher_lignes($resultats['fichier_non_mais'], $resultats['definition_possible'], $resultats['definition_ok']);
			$texte['non_mais'] .= "</div>\n</div>\n";
		}
		else {
			$texte['non_mais'] .= '<div class="success">' . "\n";
			$texte['non_mais'] .= _T('langonet:message_ok_nonmais_definis_0', array('ou_fichier' => $resultats['ou_fichier'], 'module' => $resultats['module'])) . "\n";
			$texte['non_mais'] .= "</div>\n";
		}

		// Liste des items definis sans certitude
		if (count($resultats['item_peut_etre']) > 0) {
			$texte['peut_etre'] .= '<div class="notice">' . "\n";
			if (count($resultats['item_peut_etre']) == 1) {
				$texte['peut_etre'] .= _T('langonet:message_ok_definis_incertains_1', array('langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte['peut_etre'] .= _T('langonet:message_ok_definis_incertains_n', array('nberr' => count($resultats['item_peut_etre']), 'langue' => $resultats['langue'])) . "\n";
			}
			$texte['peut_etre'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['peut_etre'] .= afficher_lignes($resultats['fichier_peut_etre']);
			$texte['peut_etre'] .= "</div>\n</div>\n";
		}
		else {
			$texte['peut_etre'] .= '<div class="success">' . "\n";
			$texte['peut_etre'] .= _T('langonet:message_ok_definis_incertains_0', array('module' => $resultats['module'])) . "\n";
			$texte['peut_etre'] .= "</div>\n";
		}
	}

	// Verification de type "Utilisation"
	else {
		// Liste des items non utilises avec certitude
		if (count($resultats['item_non']) > 0) {
			$texte['non'] .= '<div class="error">'  . "\n";
			if (count($resultats['item_non']) == 1) {
				$texte['non'] .= _T('langonet:message_ok_non_utilises_1', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte['non'] .= _T('langonet:message_ok_non_utilises_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			$texte['non'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			asort($resultats['item_non'], SORT_STRING);
			foreach($resultats['item_non'] as $_cle => $_item) {
				$texte['non'] .= "<div class=\"titrem\">\n" . $_item . "</div>\n";
			}
			$texte['non'] .= "</div>\n</div>\n";
		}
		else {
			$texte['non'] .= '<div class="success">' . "\n";
			$texte['non'] .= _T('langonet:message_ok_non_utilises_0', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			$texte['non'] .= "</div>\n";
		}

		// Liste des items non utilises sans certitude
		if (count($resultats['item_peut_etre']) > 0) {
			$texte['peut_etre'] .= '<div class="notice">' . "\n";
			if (count($resultats['item_peut_etre']) == 1) {
				$texte['peut_etre'] .= _T('langonet:message_ok_utilises_incertains_1') . "\n";
			}
			else {
				$texte['peut_etre'] .= _T('langonet:message_ok_utilises_incertains_n', array('nberr' => count($resultats['item_peut_etre']))) . "\n";
			}
			$texte['peut_etre'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['peut_etre'] .= afficher_lignes($resultats['fichier_peut_etre']);
			$texte['peut_etre'] .= "</div>\n</div>\n";
		}
		else {
			$texte['peut_etre'] .= '<div class="success">' . "\n";
			$texte['peut_etre'] .= _T('langonet:message_ok_utilises_incertains_0', array('module' => $resultats['module'])) . "\n";
			$texte['peut_etre'] .= "</div>\n";
		}
	}

	// Generation du fichier de log contenant le texte complet des resultats
	$ok = creer_log($verification, $resultats, $texte, $log_fichier);
	if (!$ok) {
		$retour['message_erreur'] .= _T('langonet:message_nok_fichier_log');
		spip_log("echec de creation du fichier $log_fichier", "langonet");
	}
	else {
		// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
		$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_log', array('log_fichier' => $log_fichier));
		$retour['message_ok']['resultats'] = $texte['non'] . $texte['non_mais'] . $texte['peut_etre'];
	}
	return $retour;
}

/**
 * Formate une liste de resultats
 *
 * @param array $tableau
 * @param array $possibles
 * @param array $ok
 * @return string
 */

// $tableau   => [item][fichier utilisant][num ligne][] => extrait ligne
// $possibles => [item][] => fichier de langue ou item est defini
// $ok        => 
function afficher_lignes($tableau, $possibles=array(), $ok=array()) {

	include_spip('inc/layer');

	// Detail des fichiers utilisant les items de langue
	ksort($tableau);
	foreach ($tableau as $item => $detail) {
		$liste_lignes .= bouton_block_depliable($item, false) .
		                 debut_block_depliable(false) .
		                 "<p style=\"padding-left:2em;\">\n  ".
		                 _T('langonet:texte_item_utilise_ou')."\n<br />";	
		foreach ($tableau[$item] as $fichier => $ligne) {
			$liste_lignes .= "\t<span style=\"font-weight:bold;padding-left:2em;\">" .$fichier. "</span><br />\n";
			foreach ($tableau[$item][$fichier] as $ligne_n => $ligne_t) {
				$L = intval($ligne_n+1);
				$T = '... '.htmlentities($ligne_t[0]).' ...';
				$liste_lignes .= "\t\t" . '<span style="padding-left:4em;text-indent: -5em;">L.'. sprintf("%04s", $L) .':</span><span style="padding-left:1em;">'.$T. "</span><br />\n";
			}
		}
		$liste_lignes .= "</p>";

		if (is_array($possibles[$item])) {
			$liste_lignes .= '<p style="padding-left:2em;">  '._T('langonet:texte_item_defini_ou')."\n<br />";
			foreach ($possibles[$item] as $fichier_def) {
				$liste_lignes .= "\t" . '<span style="font-weight:bold;padding-left:2em;">' .$fichier_def ."</span><br />\n";
			}
			$liste_lignes .= "</p>\n";
			if (!$ok[$item])
				$liste_lignes .= '<p style="padding-left:2em; color: #8a1f11"><strong>' . _T('langonet:texte_item_mal_defini') . "</strong></p>\n";
		}
		else {
			if ($ok[$item]===false)
				$liste_lignes .= '<p style="padding-left:2em; color: #8a1f11"><strong>' . _T('langonet:texte_item_non_defini') . "</strong></p>\n";
		}
		$liste_lignes .= fin_block();
	}

	return $liste_lignes;
}

/**
 * Cree le fichier de log avec le texte des resultats
 *
 * @param string $verification
 * @param array $resultats
 * @param string $texte
 * @param string &$log_fichier (nom du fichier cree retourne par reference)
 * @return boolean
 */
function creer_log($verification, $resultats, $texte, &$log_fichier) {
	// Fichier de log dans tmp/langonet/
	$log_nom = basename($resultats['langue'], '.php') . '_' . $verification[0] . '_' . date("Ymd_His").'.log';
	$log_rep = sous_repertoire(_DIR_TMP, "langonet");
	$log_rep = sous_repertoire($log_rep,"verification");
	$log_fichier = $log_rep . $log_nom;

	// Texte du fichier en UTF-8
	// -- En-tete resumant la verification
	$log_texte = "/* *****************************************************************************\n" .
	"/* LangOnet : " . entite2utf(_T('langonet:entete_log_date_creation', array('log_date_jour' => affdate(date('Y-m-d H:i:s')), 'log_date_heure' => date('H:i:s'))))."\n" .
	"/* *****************************************************************************\n" .
	"/* " . entite2utf(_T('langonet:label_verification')) . " : " . entite2utf(_T('langonet:label_verification_'.$verification)) . "\n" .
	"/* " . entite2utf(_T('langonet:label_module')) . " : " . entite2utf($resultats['module']) . "\n" .
	"/* " . entite2utf(_T('langonet:label_fichier_verifie')) . " : " . entite2utf($resultats['langue']) . "\n" .
	"/* " . entite2utf(_T('langonet:label_arborescence_scannee')) . " : " . entite2utf($resultats['ou_fichier']) . "\n" .
	"/* *****************************************************************************\n" .
	"/* " . entite2utf(_T('langonet:label_erreur')) . " : " . entite2utf(count($resultats['item_non'])) . "\n" .
	"/* " . entite2utf(_T('langonet:label_avertissement')) . " : " . entite2utf(count($resultats['item_non_mais'])+count($resultats['item_peut_etre'])) . "\n" .
	"/* *****************************************************************************\n" .
	// -- Texte des resultats: erreur (non definis ou non utilises)
	"\n\n/* *****************************************************************************\n" .
	"/* " . entite2utf(_T('langonet:entete_erreur_'.$verification)) . "\n" .
	"/* *****************************************************************************\n" .
	entite2utf(strip_tags($texte['non']));
	// -- Texte des resultats: avertissement (non definis mais n'appartenant pas au module a priori)
	if ($texte['non_mais']) {
		$log_texte .= "\n\n/* *****************************************************************************\n" .
		"/* " . entite2utf(_T('langonet:entete_avertissement_nonmais')) . "\n" .
		"/* *****************************************************************************\n" .
		entite2utf(strip_tags($texte['non_mais']));
	}
	// -- Texte des resultats: avertissement (non definis ou non utilises sans certitude)
	$log_texte .= "\n\n/* *****************************************************************************\n" .
	"/* " . entite2utf(_T('langonet:entete_avertissement_peutetre_'.$verification)) . "\n" .
	"/* *****************************************************************************\n" .
	entite2utf(strip_tags($texte['peut_etre']));

	$ok = ecrire_fichier($log_fichier, $log_texte);
	return $ok;
}

// fonction purement utilitaire
function entite2utf($sujet) {
	if (!$sujet) return;
	return utf8_encode(html_entity_decode($sujet));
}

?>