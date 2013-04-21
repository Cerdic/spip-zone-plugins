<?php
/**
 * Formulaire principal de verification des fichiers de langue
 */

// Il est anormal que cette variable puisse etre indefinie
if (!isset($GLOBALS['spip_display'])) $GLOBALS['spip_display'] = 'large';

function formulaires_langonet_verifier_charger() {

	return array('verification' => _request('verification'),
				'fichier_langue' => _request('fichier_langue'),
				'version' => _request('version'),
				'dossier_scan' => sinon(_request('dossier_scan'),array()));
}

function formulaires_langonet_verifier_verifier() {
	$erreurs = array();
	if (_request('verification') != 'fonction_l') {
		if (_request('fichier_langue') == '0') {
			$erreurs['fichier_langue'] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	if (!is_array(_request('dossier_scan')) OR count(_request('dossier_scan')) == '0') {
		$erreurs['dossier_scan'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}


function formulaires_langonet_verifier_traiter() {

	// Recuperation des champs du formulaire
	//  $verification -> type de verification 'definition' ou 'utilisation'
	//  $rep          -> nom du repertoire parent de lang/ : 'langonet' pour 'langonet/lang/'
	//                   correspond generalement au 'nom' du plugin
	//  $module       -> prefixe du fichier de langue : 'langonet' pour 'langonet_fr.php'
	//                   parfois different du 'nom' du plugin
	//  $langue       -> index du nom de langue, 'fr' pour 'langonet_fr.php'
	//  $ou_langue    -> chemin vers le fichier de langue a verifier 'plugins/auto/langonet/lang'
	//  $ou_fichier   -> racine de l'arborescence a verifier 'plugins/auto/langonet'
	$verification = _request('verification');
	$ou_fichier = _request('dossier_scan');
	$version = _request('version');
	if (($version == 'old')
	OR (($version == 'new') AND ($verification != 'fonction_l'))) {
		$retour_select_langue = explode(':', _request('fichier_langue'));
		$rep = $retour_select_langue[0];
		$module = $retour_select_langue[1];
		$langue = $retour_select_langue[2];
		$ou_langue = $retour_select_langue[3];
	}
	else {
		// Pour la vérification de la fonction _L(), on ne choisi pas de fichier de langue.
		// Néanmoins, pour créer le fichier de langue corrigé en rajoutant les nouveaux items devant remplacer
		// les appels à _L() il est nécessaire d'en choisir un.
		// Aussi, on choisit la langue de référence pour le module
	}

	// Chargement de la fonction de verification
	// et verification et formatage des resultats pour affichage
	$retour = array();
	if ($verification != 'fonction_l') {
		$langonet_verifier_items = charger_fonction('langonet_verifier_items','inc');
		$resultats = $langonet_verifier_items($rep, $module, $langue, $ou_langue, $ou_fichier, $verification);
	}
	else {
		$langonet_verifier_items = charger_fonction('langonet_verifier_l','inc');
		$resultats = $langonet_verifier_items($module, $ou_fichier);
	}

	// Creation du fichier de langue corrige avec les items detectes comme
	// non definis ou obsoletes suivant la verification en cours
	$_l = ($verification=='fonction_l');
	$all = $resultats[$_l ? "item_non" : 'item_non_mais_nok'];
	if ($all) {
		if ($verification != 'utilisation') {
			$extra = array();
			foreach ($all as $item) {
				// indefini si dejo normalise
				$index = preg_match('/^(.*)[{].*[}]$/', $item, $m) ? $m[1] : $item; 
				$extra[$index] = @$resultats['item_md5'][$item];
 			}
			$mode = $_l ?'fonction_l' :  'oublie';
		} else {
			$extra = $resultats['item_non'];
			$mode = 'inutile';
		}
		$langonet_corriger = charger_fonction('langonet_generer_fichier','inc');
		$encodage = 'utf8';
		if ($version == 'old')
			$corrections = $langonet_corriger($module, $langue, $ou_langue, $langue, $mode, $encodage, $extra);
	}

	// Traitement des resultats
	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		if ($version == 'new') {
			$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_verification');
			$retour['message_ok']['resultats'] = $resultats;
		}
		else
			$retour = formater_resultats($verification, $resultats, $corrections, $ou_fichier);
		$retour['message_ok']['explication'] = 'pas encore créée';
	}
	$retour['editable'] = true;
	return $retour;
}

/**
 * Formatage des resultats pour affichage dans le formulaire
 *
 * @param string $verification
 * @param string $resultats
 * @param string $corrections
 * @param string $langue
 * @return array
 */

// $resultats    => tableau des resultats (11 sous-tableaux) :
//                    ["module"] => intitule module
//                    ["ou_fichier"] => rep plugin
//                    ["langue"] => nom fichier de lang
//                    ["item_non"][] => intitule item
//                    ["fichier_non"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["item_non_mais_nok"][] => intitule item
//                    ["fichier_non_mais_nok"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["definition_non_mais_nok"][item][] => nom fichier de lang
//                    ["item_non_mais"][] => intitule item
//                    ["fichier_non_mais"][item][fichier utilisant][num de la ligne][] => extrait ligne
//                    ["definition_non_mais"][item][] => nom fichier de lang
//                    ["item_peut_etre"][] => intitule partiel item
//                    ["fichier_peut_etre"][item][fichier utilisant][num de la ligne][] => extrait ligne
// $verification => type de verification effectuee (definition ou utilisation)
// $corrections  => tableau des resultats de la generation du fichier de langue corrige
function formater_resultats($verification, $resultats, $corrections,$ou_fichier) {

	include_spip('inc/actions');

	// On charge le filtre de coloration si le plugin Coloration Code est actif
	// Pour un bonne presentation il faut utiliser une version >= 0.6
	$f_coloriser = NULL;
	include_spip('public/parametrer'); // inclure les fichiers fonctions
	$f_coloriser = chercher_filtre('coloration_code_color');

	// On initialise le tableau des textes resultant contenant les index:
	// - ["message_ok"]["resume"] : le message de retour ok fournissant le fichier des resultats
	// - ["message_ok"]["resultats"] : le texte des résultats
	// - ["message_erreur"] : le message d'erreur si on a erreur de traitement pendant l'execution
	$retour = array();

	$texte = array('non' => '', 'non_mais' => '', 'non_mais_nok' => '', 'peut_etre' => '');
	if ($verification == 'definition') {
		// Liste des items du module en cours de verification
		// et non definis avec certitude dans le fichier idoine
		if (count($resultats['item_non']) > 0) {
			$texte['non'] .= '<div class="error">'  . "\n";
			if (count($resultats['item_non']) == 1) {
				$texte['non'] .= _T('langonet:message_ok_non_definis_1', array('module' => $resultats['module'], 'ou_fichier' => join(' ', $resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte['non'] .= _T('langonet:message_ok_non_definis_n', array('module' => $resultats['module'], 'nberr' => count($resultats['item_non']), 'ou_fichier' => join(' ', $resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
			}
			$texte['non'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['non'] .= afficher_lignes('non', $resultats['fichier_non'], array(), $f_coloriser);
			$texte['non'] .= "</div><br />\n";
			if($corrections['fichier'])
				$texte['non'] .= bouton_action(_T('langonet:bouton_corriger'), 
											generer_action_auteur('langonet_telecharger', $corrections['fichier']),
											"", "", _T('langonet:bulle_corriger'));
			$texte['non'] .= "</div>\n";
		}
		else {
			$texte['non'] .= '<div class="success">' . "\n";
			$texte['non'] .= _T('langonet:message_ok_non_definis_0', array('module' => $resultats['module'], 'ou_fichier' => join(' ', $resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
			$texte['non'] .= "</div>\n";
		}

		// Liste des items n'appartenant pas au module en cours de verification 
		// et non definis avec certitude dans le fichier idoine
		if (count($resultats['item_non_mais_nok']) > 0) {
			$texte['non_mais_nok'] .= '<div class="error">'  . "\n";
			if (count($resultats['item_non_mais_nok']) == 1) {
				$texte['non_mais_nok'] .= _T('langonet:message_ok_nonmaisnok_definis_1', array('ou_fichier' => join(' ', $resultats['ou_fichier']), 'module' => $resultats['module'])) . "\n";
			}
			else {
				$texte['non_mais_nok'] .= _T('langonet:message_ok_nonmaisnok_definis_n', array('nberr' => count($resultats['item_non_mais_nok']), 'ou_fichier' => join(' ', $resultats['ou_fichier']), 'module' => $resultats['module'])) . "\n";
			}
			$texte['non_mais_nok'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['non_mais_nok'] .= afficher_lignes('non_mais_nok', $resultats['fichier_non_mais_nok'], $resultats['definition_non_mais_nok'], $f_coloriser);
			$texte['non_mais_nok'] .= "</div>\n</div>\n";
		}
		else {
			$texte['non_mais_nok'] .= '<div class="success">' . "\n";
			$texte['non_mais_nok'] .= _T('langonet:message_ok_nonmaisnok_definis_0', array('ou_fichier' => join(' ', $resultats['ou_fichier']), 'module' => $resultats['module'])) . "\n";
			$texte['non_mais_nok'] .= "</div>\n";
		}

		// Liste des items n'appartenant pas au module en cours de verification 
		// mais definis dans le module idoine
		if (count($resultats['item_non_mais']) > 0) {
			$texte['non_mais'] .= '<div class="notice">' . "\n";
			if (count($resultats['item_non_mais']) == 1) {
				$texte['non_mais'] .= _T('langonet:message_ok_nonmais_definis_1', array('ou_fichier' => join(' ', $resultats['ou_fichier']), 'module' => $resultats['module'])) . "\n";
			}
			else {
				$texte['non_mais'] .= _T('langonet:message_ok_nonmais_definis_n', array('nberr' => count($resultats['item_non_mais']), 'ou_fichier' => join(' ', $resultats['ou_fichier']), 'module' => $resultats['module'])) . "\n";
			}
			$texte['non_mais'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['non_mais'] .= afficher_lignes('non_mais', $resultats['fichier_non_mais'], $resultats['definition_non_mais'], $f_coloriser);
			$texte['non_mais'] .= "</div>\n</div>\n";
		}
		else {
			$texte['non_mais'] .= '<div class="success">' . "\n";
			$texte['non_mais'] .= _T('langonet:message_ok_nonmais_definis_0', array('ou_fichier' => join(' ', $resultats['ou_fichier']), 'module' => $resultats['module'])) . "\n";
			$texte['non_mais'] .= "</div>\n";
		}

		// Liste des items non definis sans certitude car utilises dans un contexte variable
		if (count($resultats['item_peut_etre']) > 0) {
			$texte['peut_etre'] .= '<div class="notice">' . "\n";
			if (count($resultats['item_peut_etre']) == 1) {
				$texte['peut_etre'] .= _T('langonet:message_ok_definis_incertains_1', array('langue' => $resultats['langue'])) . "\n";
			}
			else {
				$texte['peut_etre'] .= _T('langonet:message_ok_definis_incertains_n', array('nberr' => count($resultats['item_peut_etre']), 'langue' => $resultats['langue'])) . "\n";
			}
			$texte['peut_etre'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['peut_etre'] .= afficher_lignes('peut_etre', $resultats['fichier_peut_etre'], array(), $f_coloriser);
			$texte['peut_etre'] .= "</div>\n</div>\n";
		}
		else {
			$texte['peut_etre'] .= '<div class="success">' . "\n";
			$texte['peut_etre'] .= _T('langonet:message_ok_definis_incertains_0', array('module' => $resultats['module'])) . "\n";
			$texte['peut_etre'] .= "</div>\n";
		}
	}

	// Verification de type "Utilisation"
	else if ($verification == 'utilisation') {
		// Liste des items non utilises avec certitude
		if (count($resultats['item_non']) > 0) {
			$texte['non'] .= '<div class="error">'  . "\n";
			if (count($resultats['item_non']) == 1) {
				if(count($resultats['ou_fichier']) > 1)
					$texte['non'] .= _T('langonet:message_ok_non_utilises_1s', array('ou_fichier' => implode(', ',$resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
				else
					$texte['non'] .= _T('langonet:message_ok_non_utilises_1', array('ou_fichier' => implode(', ',$resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
			}
			else {
				if(count($resultats['ou_fichier']) > 1)
					$texte['non'] .= _T('langonet:message_ok_non_utilises_ns', array('nberr' => count($resultats['item_non']), 'ou_fichier' => implode(', ',$resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
				else
					$texte['non'] .= _T('langonet:message_ok_non_utilises_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => implode(', ',$resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
			}
			$texte['non'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			asort($resultats['item_non'], SORT_STRING);
			foreach($resultats['item_non'] as $_cle => $_item) {
				$texte['non'] .= "<div class=\"titrem\">\n" . $_item . "</div>\n";
			}
			$texte['non'] .= "</div><br />\n";
			if($corrections['fichier'])
				$texte['non'] .= bouton_action(_T('langonet:bouton_corriger'), 
								generer_action_auteur('langonet_telecharger', $corrections['fichier']),
								"", "", _T('langonet:bulle_corriger'));
			$texte['non'] .= "</div>\n";
		}
		else {
			$texte['non'] .= '<div class="success">' . "\n";
			if(count($resultats['ou_fichier']) > 1)
				$texte['non'] .= _T('langonet:message_ok_non_utilises_0s', array('ou_fichier' => implode(', ',$resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
			else
				$texte['non'] .= _T('langonet:message_ok_non_utilises_0', array('ou_fichier' => implode(', ',$resultats['ou_fichier']), 'langue' => $resultats['langue'])) . "\n";
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
			$texte['peut_etre'] .= afficher_lignes('peut_etre', $resultats['fichier_peut_etre'], array(), $f_coloriser);
			$texte['peut_etre'] .= "</div>\n</div>\n";
		}
		else {
			$texte['peut_etre'] .= '<div class="success">' . "\n";
			$texte['peut_etre'] .= _T('langonet:message_ok_utilises_incertains_0', array('module' => $resultats['module'])) . "\n";
			$texte['peut_etre'] .= "</div>\n";
		}
	}

	// Verification de type "Fonction_L"
	else {
		// Liste des cas d'utilisation de la fonction _L()
		if (count($resultats['item_non']) > 0) {
			$texte['non'] .= '<div class="error">'  . "\n";
			if (count($resultats['item_non']) == 1) {
				$texte['non'] .= _T('langonet:message_ok_fonction_l_1', array('ou_fichier' => join(' ', $resultats['ou_fichier']))) . "\n";
			}
			else {
				$texte['non'] .= _T('langonet:message_ok_fonction_l_n', array('nberr' => count($resultats['item_non']), 'ou_fichier' => join(' ', $resultats['ou_fichier']))) . "\n";
			}
			$texte['non'] .= '<div style="background-color: #fff; margin-top: 10px;">' . "\n";
			$texte['non'] .= afficher_lignes('non', $resultats['fichier_non'], $resultats['item_md5'], $f_coloriser);
			$texte['non'] .= "</div><br />\n";
			if($corrections['fichier'])
				$texte['non'] .= bouton_action(_T('langonet:bouton_corriger'), 
							generer_action_auteur('langonet_telecharger', $corrections['fichier']),
							"", "", _T('langonet:bulle_corriger'));
			$texte['non'] .= "</div>\n";
		}
		else {
			$texte['non'] .= '<div class="success">' . "\n";
			$texte['non'] .= _T('langonet:message_ok_fonction_l_0', array('ou_fichier' => join(' ', $resultats['ou_fichier']))) . "\n";
			$texte['non'] .= "</div>\n";
		}
	}

	// Generation du fichier de log contenant le texte complet des resultats
	$resume = 'langonet:message_ok_fichier_log';
	$ok = creer_log($verification, $resultats, $texte, $log_fichier);
	if (!$ok) {
		$retour['message_erreur'] .= _T('langonet:message_nok_fichier_log');
		spip_log("echec de creation du fichier $log_fichier", "langonet");
		return $retour;
	}

	if ($verification == 'fonction_l' OR $verification == 'definition' ) {
		// On cree un fichier de script capable de modifier les fichiers de l'arbo specifiee
		//  Il porte le meme nom que le log avec l'extension .sh
		$script = substr($log_fichier, 0, strlen($log_fichier)-4) . '.sh';
		if ($contenu = creer_script($resultats, $verification)) {
			if (!ecrire_fichier($script, $contenu)) {
				$retour['message_erreur'] .= _T('langonet:message_nok_fichier_script');
				spip_log("echec de creation du fichier $script", "langonet");
				return $retour;
			}
			$resume = 'langonet:message_ok_fichier_log_script';
		}
	}

	// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
	$retour['message_ok']['resume'] = _T($resume, array('log_fichier' => $log_fichier, 'script' => $script));
	$retour['message_ok']['resultats'] = $texte['non'] . $texte['non_mais_nok'] . $texte['non_mais'] . $texte['peut_etre'];

	return $retour;
}

/**
 * Formate une liste de resultats
 *
 * @param string $type
 * @param array $tableau
 * @param array $extra
 * @param string $f_coloriser
 * @return string
 */

/// $type	=> le type de resultats (non, non_mais, non_mais_nok, peut_etre)
/// $tableau   	=> [item][fichier utilisant][num ligne][] => resultats des preg_match
/// $extra	=> [item][] => fichier de langue ou item est defini
///		ou [codage(item)] => item, ou l'item est l'argument de _L()
/// $f_coloriser=> la fonction de colorisation ou NULL si le plugin coloration_code n'est pas actif  

function afficher_lignes($type, $tableau, $extra=array(), $f_coloriser) {

	include_spip('inc/layer');

	ksort($tableau);
	foreach ($tableau as $k => $v) {
		$occ = langonet_lister_occ($type, $k, $v, $extra, $f_coloriser);
		$brut = preg_match('/^(.*)[{].*[}]$/', $k, $m) ? $m[1]:$k;
		if (!$occ)
		  $tableau[$k] = "<div class='titrem'>\n$brut</div>\n";
		else
		  $tableau[$k] = bouton_block_depliable($brut, false) .
			debut_block_depliable(false) . 
			$occ .
			fin_block();
	}
	return join('', $tableau);
}

/**
 * Formate les occurrences d'un item dans les fichiers
 *
 * @param string $type
 * @param string $item
 * @param array $detail
 * @param array $extra
 * @param string $f_coloriser
 * @return string
 */

function langonet_lister_occ($type, $item, $detail, $extra, $f_coloriser)
{
	$occ = '';

	foreach ($detail as $fichier => $lignes) {
		$occ .= "\t<span style=\"font-weight:bold;padding-left:2em;\">" .$fichier. "</span><br />\n";
		foreach ($lignes as $ligne_n => $ligne_t) {
			$L = sprintf("%04s", intval($ligne_n+1));
			// Il peut y en avoir plusieurs sur une meme ligne,
			// on n'affiche que le premier
			$match = $ligne_t[0];
			$T = '... '.trim($match[0]).' ...';
			if ($f_coloriser) {
				// Traitement de la coloration de l'extrait.
				// C'est la fonction de coloration qui s'occupe des entites html
				$infos = pathinfo($fichier);
				$extension = ($infos['extension'] == 'html') ? 'html4strict' : $infos['extension'];
				$T = $f_coloriser($T,  $extension, 'code', 'span');
			} else $T = htmlspecialchars($T);

			$occ .= "\t\t<code style='padding-left:4em;text-indent: -5em;'>L$L : $T</code><br />\n";
		}
	}

	if ($occ) $occ = _T('langonet:texte_item_utilise_ou')."\n<br />" . $occ;

	// Quand l'index ne correspond pas aux occurrences (on prend la derniere)
	// typiquement quand c'est un md5, donner l'index prevu pour aider a trouver l'homonyme
	// (mais ce serait encore mieux que Langonet le donne)
	if ($match[2] AND ($item !==  $match[2])) {
		include_spip('inc/langonet_utils');
		$index = langonet_index_brut($match[2]);
		$occ = "(<b>" . $index . "</b>)<br />" . $occ;
	}

	if ($occ) $occ = "<p style=\"padding-left:2em;\">\n$occ</p>";

	if ($type != 'non' AND isset($extra[$item])) {
		$occ .= "<p style=\"padding-left:2em;\">  " . (($type=='non_mais_nok') ? _T('langonet:texte_item_mal_defini') : _T('langonet:texte_item_defini_ou')) . "\n<br />";
		foreach ($extra[$item] as $fichier_def) {
			$occ .= "\t<span style=\"font-weight:bold;padding-left:2em;\">" .$fichier_def. "</span><br />\n";
		}
		$occ .= "</p>\n";
	} elseif ($type == 'non_mais_nok') {
		$occ .= "<p style=\"padding-left:2em;\">  " . _T('langonet:texte_item_non_defini') . "</p>\n<br />\n";
	}
	return $occ;
}

/**
 * Cree le fichier de log avec le texte des resultats.
 *
 * @param string $verification
 * @param array $resultats
 * @param string $texte
 * @param string &$log_fichier (nom du fichier cree retourne par reference)
 * @return boolean
 */
function creer_log($verification, $resultats, $texte, &$log_fichier) {
	// Fichier de log dans tmp/langonet/
	$ou_fichier =  $resultats['ou_fichier'];
	$log_prefixe = ($verification == 'fonction_l') ? str_replace("/", "%", implode('_',$ou_fichier)) : basename($resultats['langue'], '.php') . '_';
	$log_nom = $log_prefixe . $verification[0] . '_' . date("Ymd_His").'.log';
	$log_rep = sous_repertoire(_DIR_TMP, "langonet");
	$log_rep = sous_repertoire($log_rep, "verification");
	$log_rep = sous_repertoire($log_rep, $verification);
	$log_fichier = $log_rep . $log_nom;

	// Texte du fichier en UTF-8
	include_spip('inc/langonet_utils');

	$sep = str_repeat('*', 77);

	// -- En-tete resumant la verification
	$log_texte = "# $sep\n# LangOnet : " . 
		entite2utf(_T('langonet:entete_log_date_creation', array('log_date_jour' => affdate(date('Y-m-d H:i:s')), 'log_date_heure' => date('H:i:s')))) .
		"\n# $sep\n# " .
		entite2utf(_T('langonet:label_verification')) . " : " .
		entite2utf(_T('langonet:label_verification_'.$verification)) .
		"\n";

	if ($verification != 'fonction_l') {
		$log_texte .= "# " . 
			entite2utf(_T('langonet:label_module')) . " : " .
			entite2utf($resultats['module']) . "\n# " .
			entite2utf(_T('langonet:label_fichier_verifie')) . " : " .
			entite2utf($resultats['langue']) . "\n";
	}

	$log_texte .= "# " .
		entite2utf(_T('langonet:label_arborescence_scannee')) . " : \n# - " .
		entite2utf(implode("\n# - ",$ou_fichier)) . 
		"\n#\n# $sep\n# " .
		entite2utf(_T('langonet:label_erreur')) . " : " .
		strval(count($resultats['item_non'])+count($resultats['item_non_mais_nok']));

	if ($verification != 'fonction_l') {
		$log_texte .= "\n# " . entite2utf(_T('langonet:label_avertissement')) . " : " . strval(count($resultats['item_non_mais'])+count($resultats['item_peut_etre']));
	}
	$log_texte .= "\n# $sep\n";

	// -- Texte des resultats: erreur (non definis ou non utilises)
	$log_texte .= "\n# $sep\n# " .
		entite2utf(_T('langonet:entete_log_erreur_'.$verification)) .
		"\n# $sep\n" .
		texte2log($texte['non']);

	// -- Texte des resultats: erreur (non definis mais n'appartenant pas au module en cours de verification)
	if ($verification == 'definition') {
		$log_texte .= "\n\n# $sep\n# " . 
		  entite2utf(_T('langonet:entete_log_erreur_definition_nonmais')) .
		  "\n# $sep\n" .
		  texte2log($texte['non_mais_nok']);

	// -- Texte des resultats: avertissement (definis mais dans un autre module)
		$log_texte .= "\n\n# $sep\n# " . 
		  entite2utf(_T('langonet:entete_log_avertissement_nonmais')) .
		  "\n# $sep\n" .
		  texte2log($texte['non_mais']);
	}

	// -- Texte des resultats: avertissement (non definis ou non utilises sans certitude)
	if ($verification != 'fonction_l') {
		$log_texte .= "\n\n# $sep\n# " . 
			entite2utf(_T('langonet:entete_log_avertissement_peutetre_'.$verification)) . 
			"\n# $sep\n" .
			texte2log($texte['peut_etre']);
	}
	$log_texte = wordwrap($log_texte, 80, "\n", true);
	$ok = ecrire_fichier($log_fichier, $log_texte);
	return $ok;
}

/**
 * Construit un script Shell 
 * s'appliquant sur les fichiers contenant les chaines indefinies
 * @param array $resultats
 * @param string $verification 
 * @return boolean
 */
function creer_script($resultats, $verification) {
	$ou = join(' ',$resultats['ou_fichier']);
	$prefixe = ($resultats['module'] == 'spip' 
				OR $resultats['module'] == 'ecrire' 
				OR $resultats['module'] == 'public') ? '' : $resultats['module'] . ':' ;

	$files = $sed = array();
	$_l = ($verification=='fonction_l');
	$all = $resultats[$_l ? "fichier_non" : 'fichier_non_mais_nok'];
	// Pour chaque item on construit le sed
	// et on collecte au passage les fichiers qui le contiennent
	if (is_array($all)) foreach ($all as $index => $val) {
		foreach($val as $f => $l) $files[$f]= str_replace(_DIR_RACINE . $ou, '', $f);
		$fichier = key($val);	
		$val = array_shift($val); // premier fichier
		$val = array_shift($val); // premiere ligne du dit
		$val = array_shift($val); // premier match dans la dite
		$val = array_shift($val); // index 0 du dit
		$args = '';
		if ($_l) {
			include_spip('inc/langonet_verifier_l');
			if (preg_match(_LANGONET_FONCTION_L2, $val, $m))
				$occ = $m[2];
			elseif (!preg_match(_LANGONET_PATTERN_FONCTION_L, $val, $m))
				continue;
			else {
			// gestion des backslash imparfaite, mais c'est deja ca
				$occ = str_replace('\\', '.', $m[2]);
				// insuffisant au niveau du Sed
				if (preg_match_all("/'[$](\w[][\w\d)]*?)'/", $occ, $m)) {
				  $args = array();
				  foreach($m[1] as $s) {
				    $args[]= "'$s' => \\\$$s";
				    $occ = str_replace("'\$$s'", "'\\\$$s'", $occ);    
				  }
				  $args = ", array(" . join(', ', $args) . ')';
				}
			}
		} else {
			// si c'est un <: :> normaliser au besoin
			if ($val[0]!=='<') continue;
			if (!preg_match(_LANGONET_ITEM_H, $val, $m)) continue;
			if (preg_match(',^\w+$,', $occ = $m[2])) continue;
		}
		// Un item avec $ non transforme n'est pas gere
		// (le cas mixte pose pb, mais bof)
		if ((strpos($occ, '$') !== false) AND !$args) continue;
		$occ = str_replace('%', '\\%', str_replace('[', '\\[', $occ));
		$cite = $_l ? "s%_L *( *.$occ *.%_T('$prefixe$index'$args%;" : "s%<:$occ%<:$prefixe$index%;";
		$sed[$cite]=strlen($occ); 
	}
	if (!$sed) return '';

	// Trier par ordre decroissant des longueurs des items a remplacer:
	// "truc_x" doit etre traite avant "truc"
	arsort($sed);
	// Creer le texte du script
	$in = 'executer ce script dans ';
	$out = "Si correct, rappeler ce script avec 'mv' comme argument pour modifier les fichiers.";
	return "echo \"$in $ou\"\n" .
		'if [ "$*" == "mv" ]; then comm=mv; else comm=diff; fi' .
		"\nfor i in " .
		join(" ", $files) .
		"\ndo\necho \$i\nr=\$(basename \$i)\nsed \"\n" .
		join("\n", array_keys($sed)) .
		"\n\" \$i > /tmp/\$r\n\$comm /tmp/\$r \$i\ndone\n" .
		"\nif [ \"$*\" != 'mv' ]; then echo; echo \"$out\"; fi";
}

// fonction purement utilitaire
function texte2log($texte) {
	// On vire les tags
	$texte_log = strip_tags($texte);
	// On passe en utf-8
	$texte_log = entite2utf($texte_log);

	return $texte_log;
}

?>