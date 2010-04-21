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

	// Les REGEXP de recherche de l'item de langue (voir le fichier regexp.txt)
	// pour les fichiers .html et .php
	define("_TROUVER_ITEM_HP", ",(?:<:|_[T|U]\(['\"])(?:([a-z0-9_]+):)?([a-z0-9_]+)((?:{(?:[^\|=>]*=[^\|>]*)})?(?:(?:\|[^>]*)?)(?:['\"]\s*\.\s*[^\s]+)?),iS");
	// pour les fichiers .xml
	define("_TROUVER_ITEM_X", ",<[a-z0-9_]+>[\n|\t|\s]*([a-z0-9_]+):([a-z0-9_]+)[\n|\t|\s]*</[a-z0-9_]+()>,iS");

	// Verification et formatage des resultats pour affichage
	$retour = array();
	$resultats = $langonet_verifier_items($rep, $module, $langue, $ou_langue, $ou_fichier, $verification);
	if ($resultats['erreur']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour = formater_resultats($resultats, $verification);
	}
	$retour['editable'] = true;
	return $retour;
}

/**
 * Verification de l'utilisation des items de langue
 *
 * @param string $resultats
 * @param string $verification
 * @return string
 */

// $resultats    => tableau des resultats (9 sous-tableaux) :
//                    ["module"] => intitule module
//                    ["ou_fichier"] => rep plugin
//                    ["langue"] => nom fichier de lang
//                    ["item_non"][] => intitule item
//                    ["fichier_non"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["item_peut_etre"][] => intitule partiel item
//                    ["fichier_peut_etre"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["definition_possible"][item][] =>nom fichier de lang
//                    ["statut"] => (bool)
// $verification => type de verification effectuee
function formater_resultats($resultats, $verification='definition') {

	// On initialise le tableau des textes resultant contenant les index:
	// - 'message_ok' : le message de retour ok fournissant le fichier des resultats
	// - 'message_erreur' : le message d'erreur si on a erreur de traitement pendant l'execution
	// - 'message_resultats' : le texte des resultats correctement formate
	$retour = array();

	$texte = '';
	if ($verification == 'definition') {
		// Liste des items non definis avec certitude et bien utilises avec le bon module
		if (count($resultats['item_non']) > 0) {
			$texte .= '<div class="error">'  . "\n";
			if (count($resultats['item_non']) == 1) {
				$texte .= _T('langonet:message_ok_non_definis_1', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_non_definis_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			$texte .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte .= afficher_lignes($resultats['fichier_non']);
			$texte .= "</div>\n</div>\n";
		}
		else {
			$texte .= '<div class="success">' . "\n";
			$texte .= _T('langonet:message_ok_non_definis_0', array('module' => $resultats['module'], 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			$texte .= "</div>\n";
		}

		// Liste des items non definis mais utilises avec un module different de celui 
		// en cours de verification
		if (count($resultats['item_non_mais']) > 0) {
			$texte .= '<div class="notice">' . "\n";
			if (count($resultats['item_non_mais']) == 1) {
				$texte .= _T('langonet:message_ok_nonmais_definis_1', array('ou_fichier' => $resultats['ou_fichier'], 'module' => $resultats['module'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_nonmais_definis_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'module' => $resultats['module'])) . "\n";
			}
			$texte .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte .= afficher_lignes($resultats['fichier_non_mais'], $resultats['definition_possible']);
			$texte .= "</div>\n</div>\n";
		}
		else {
			$texte .= '<div class="success">' . "\n";
			$texte .= _T('langonet:message_ok_nonmais_definis_0', array('ou_fichier' => $resultats['ou_fichier'], 'module' => $resultats['module'])) . "\n";
			$texte .= "</div>\n";
		}

		// Liste des items definis sans certitude
		if (count($resultats['item_peut_etre']) > 0) {
			$texte .= '<div class="notice">' . "\n";
			if (count($resultats['item_peut_etre']) == 1) {
				$texte .= _T('langonet:message_ok_definis_incertains_1', array('langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_definis_incertains_n', array('nberr' => count($resultats['item_peut_etre']), 'langue' => $resultats['langue'])) . "\n";
			}
			// on ferme le <p> ouvert au-dessus car ce qui suit est un <div>
			$texte .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte .= afficher_lignes($resultats['fichier_peut_etre']);
			$texte .= "</div>\n</div>\n";
		}
		else {
			$texte .= '<div class="success">' . "\n";
			$texte .= _T('langonet:message_ok_definis_incertains_0', array('module' => $resultats['module'])) . "\n";
			$texte .= "</div>\n</div>\n";
		}
	}

	// Verification de type "Utilisation"
	else {
		// Liste des items non utilises avec certitude
		if (count($resultats['item_non']) > 0) {
			if (count($resultats['item_non']) == 1) {
				$texte .= _T('langonet:message_ok_non_utilises_1', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_non_utilises_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			}
			// on ferme le <p> ouvert dans langonet_verifier.html
			// car ce qui suit sont des <div>
			$texte .= "</p>\n";
			asort($resultats['item_non'], SORT_STRING);
			foreach($resultats['item_non'] as $_cle => $_item) {
				$texte .= "<div class=\"titrem\">\n" . $_item . "</div>\n";
			}
		}
		else {
			$texte .= _T('langonet:message_ok_non_utilises_0', array('ou_fichier' => $resultats['ou_fichier'], 'langue' => $resultats['langue'])) . "\n";
			$texte .= "</p>\n"; // <p> ouvert dans langonet_verifier.html
		}

		// Liste des items utilises sans certitude
		if (count($resultats['item_peut_etre']) > 0) {
			$texte .= "<p class=\"reponse_formulaire reponse_formulaire_erreur\">\n<br />";
			if (count($resultats['item_peut_etre']) == 1) {
				$texte .= _T('langonet:message_ok_utilises_incertains_1') . "\n";
			}
			else {
				$texte .= _T('langonet:message_ok_utilises_incertains_n', array('nberr' => count($resultats['item_peut_etre']))) . "\n";
			}
			// on ferme le <p> ouvert au-dessus car ce qui suit est un <div>
			$texte .= "\n</p>\n";
			$texte .= afficher_lignes($resultats['fichier_peut_etre']);
			// on ouvre un <p> ici qui sera ferme dans langonet_verifier.html
			$texte .= "\n<p>\n";
		}
		else {
			$texte .= "<p class=\"reponse_formulaire reponse_formulaire_ok\">\n<br />";
			$texte .= _T('langonet:message_ok_utilises_incertains_0', array('module' => $resultats['module'])) . "\n";
			// pas de </p> ici : il sera ferme dans langonet_verifier.html
		}
	}

	// Generation du fichier de log contenant le texte complet des resultats
//	$log_nom = md5($verification{0}.$resultats['langue'].$resultats['ou_fichier']).'.txt';
	$log_nom = basename($resultats['langue'], '.php') . '_' . $verification{0} . '_' . date("Ymd_His").'.txt';
	$log_rep = sous_repertoire(_DIR_TMP, "langonet");
	$log_fichier = $log_rep . $log_nom;
	$log_texte = "langOnet : ";
	$log_texte .= utf8_encode(html_entity_decode(_T('langonet:bak_date_crea', array('bak_date_jour' => affdate(date('Y-m-d H:i:s')), 'bak_date_heure' => date('H:i:s')))))."\n\n";
	$log_texte .= utf8_encode(html_entity_decode(strip_tags($texte)));
	$ok = ecrire_fichier($log_fichier, $log_texte);
	if (!$ok) {
		$retour['message_erreur'] .= _T('langonet:message_nok_fichier_log', array('log_fichier' => $log_rep.$log_nom));
		spip_log("echec de creation du fichier $log_nom", "langonet", $log_rep);
	}
	else {
		// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
		$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_log', array('log_fichier' => $log_rep.$log_nom));
		$retour['message_ok']['resultats'] = $texte;
	}
	return $retour;
}

/**
 * Formate une liste de resultats
 *
 * @param array $tableau
 * @param array $possibles
 * @return string
 */

// $tableau   => [item][fichier utilisant][num ligne][] => extrait ligne
// $possibles => [item][] => fichier de langue ou item est defini
function afficher_lignes($tableau, $possibles=array()) {
	include_spip('inc/layer');
	// detail des fichiers utilisant les items de langue
	ksort($tableau);
	foreach ($tableau as $item => $detail) {
		$liste_lignes .= bouton_block_depliable($item, false);
		$liste_lignes .= debut_block_depliable(false);
		$liste_lignes .= "<p style=\"padding-left:2em;\">\n  "._T('langonet:item_utilise_ou')."\n<br />";	
		foreach ($tableau[$item] as $fichier => $ligne) {
			$liste_lignes .= "\t<span style=\"font-weight:bold;padding-left:2em;\">" .$fichier. "</span><br />\n";
			foreach ($tableau[$item][$fichier] as $ligne_n => $ligne_t) {
				$L = intval($ligne_n+1);
				$T = '... '.htmlentities($ligne_t[0]).' ...';
				$liste_lignes .= "\t\t<span style=\"padding-left:4em;text-indent: -5em;\">L.". sprintf("%04s", $L) .":</span><span style=\"padding-left:1em;\">".$T. "</span><br />\n";
			}
		}
		$liste_lignes .= "</p>";

		if (is_array($possibles[$item])) {
			$liste_lignes .= "<p style=\"padding-left:2em;\">  "._T('langonet:definition_possible')."\n<br />";
			foreach ($possibles[$item] as $fichier_def) {
				$liste_lignes .= "\t<span style=\"font-weight:bold;padding-left:2em;\">" .$fichier_def. "</span><br />\n";
			}
			$liste_lignes .= "</p>\n";
		}
		$liste_lignes .= fin_block();
	}

	return $liste_lignes;
}
?>