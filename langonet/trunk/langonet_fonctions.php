<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_DOSSIERS_EXCLUS')) {
	/**
	 * Permet de définir les arborescences à exclure du scan des fichiers de langue.
	 * Les arborescences sont exprimées par une chaine simple (ecrire, plugins_dist, plugins...).
	 * Le délimiteur est le ':'.
	 * Les valeurs sont :
	 * - ecrire
	 * - prive
	 * - squelettes_dist
	 * - plugins ou auto (pour plugins/auto uniquement)
	 * - plugins_dist
	 * - plugins_suppl
	 * - squelettes
	 */
	define('_LANGONET_DOSSIERS_EXCLUS', '');
}

/**
 * Creation de la liste des fichiers de langue generes
 *
 * @return array
 */
function langonet_lister_fichiers_lang($operation='generation') {

	// On cherche le dernier fichier de log de chaque fichier de langue
	$liste_tous = array();
	$sous_dir = (!$operation OR ($operation == 'generation')) ? 'generation/': "verification/${operation}/";
	$langues = preg_files(_DIR_TMP . "langonet/${sous_dir}", '[^/]*_[\w{2,3}]*.php$');
	foreach ($langues as $_fichier) {
		$liste_tous[basename($_fichier, '.php')] = $_fichier;
	}

	return $liste_tous;
}


/**
 * Bulle d'information des liens de telechargement
 *
 * @param string $fichier
 * @param string $type
 * @param string $action
 * @return string
 */

function langonet_creer_bulle_fichier($fichier, $type='lang', $action='telecharger') {

	// Date du fichier formatee
	$date = affdate_heure(date('Y-m-d H:i:s', filemtime($fichier)));
	// Bulle d'information suivant le type de fichier (log ou langue)
	$bulle = _T('langonet:bulle_' . $action . '_fichier_' . $type, array('date' => $date));

	return $bulle;
}

/**
 * Creation du select des fichiers de langue
 *
 * @param string $sel_l
 * @return array
 */
function langonet_creer_select_langues($sel_l='0', $exclure_paquet=true) {
	$retour = creer_selects($sel_l, array(), $exclure_paquet);
	return $retour['fichiers'];
}

/**
 * Creation du select des arborescences a scanner
 *
 * @param string $sel_d
 * @return array
 */
function langonet_creer_select_dossiers($sel_d=array(), $multiple=true) {
	if (is_string($sel_d)) $sel_d = array($sel_d);
	$retour = creer_selects('0', $sel_d, true, $multiple);
	return $retour['dossiers'];
}


function langonet_cadrer_expression($expression, $colonne, $ligne, $fichier, $cadre=4) {
	$affiche = '';

	if ($ligne) {
		$debut = max($colonne-$cadre, 0);
		// il faut calculer la taille exacte du préfixe : $colonne-$debut
		$affiche = substr($ligne, $debut, $colonne - $debut + strlen($expression) + $cadre);

		// Si la taille précisée excède la distance entre le $debut et la fin de la ligne, substr() coupe
		// avant sans retourner d'erreur. Il faut donc calculer l'index final
		$index_fin = $debut + strlen($affiche);

		$coloriser = NULL;
		include_spip('public/parametrer'); // inclure les fichiers fonctions
		$coloriser = chercher_filtre('coloration_code_color');

		if ($coloriser) {
			// Traitement de la coloration de l'extrait.
			// C'est la fonction de coloration qui s'occupe des entites html
			$infos = pathinfo($fichier);
			$extension = ($infos['extension'] == 'html') ? 'html4strict' : $infos['extension'];
			$affiche = $coloriser($affiche,  $extension, 'code', 'span');
		}
		else {
			$affiche = '<code>' . htmlspecialchars($affiche) . '</code>';
		}

		// On encadre l'expression par des points avant et après sauf si on a déjà atteint le bout
		// On fait ce traitement après le htmlspecialchars pour éviter que les points de suspension
		// ne soient traduits en entité html. Par contre la détection du besoin de suspension s'est
		// faite avant sur la chaine native.
		$suspension = '&#8230;';
		$affiche = ($debut > 0 ? $suspension : '') . trim($affiche) . ($index_fin < strlen($ligne)-1 ? $suspension : '');
	}

	return $affiche;
}




/**
 * Creation d'un tableau des selects:
 * - des fichiers de langue
 * - des arborescences a scanner
 *
 * @param string $sel_l option du select des langues
 * @param array $sel_d option(s) du select des repertoires
 * @return array
 */
function creer_selects($sel_l='0',$sel_d=array(), $exclure_paquet=true, $multiple=true) {
	include_spip('inc/outiller');

	// On récupère la liste des dossiers exclus du scan.
	$rep_exclus = explode(':', _LANGONET_DOSSIERS_EXCLUS);

	// Recuperation des repertoires des plugins par défaut
	if(in_array('auto', $rep_exclus)){
		$rep_plugins = lister_dossiers_plugins();
		foreach ($rep_plugins as $key => $rep_plugin) {
			$rep_plugins[$key] = str_replace('auto/','',$rep_plugin);
		}
		$rep_auto = lister_dossiers_plugins(_DIR_PLUGINS_AUTO);
		$rep_plugins = array_diff_assoc($rep_plugins, $rep_auto);
	} else {
		$rep_plugins = !in_array('plugins', $rep_exclus)
			? lister_dossiers_plugins()
			: array();
	}
	// Recuperation des repertoires des extensions : _DIR_PLUGINS_DIST à partir de SPIP 3
	$rep_extensions = !in_array('plugins_dist', $rep_exclus)
		? lister_dossiers_plugins(_DIR_PLUGINS_DIST)
		: array();
	// Recuperation des repertoires des plugins supplémentaires en mutualisation : _DIR_PLUGINS_SUPPL
	$rep_suppl = (!in_array('plugins_suppl', $rep_exclus) and defined('_DIR_PLUGINS_SUPPL'))
		? lister_dossiers_plugins(_DIR_PLUGINS_SUPPL)
		: array();
	// Recuperation des repertoires squelettes perso
	$rep_perso = array();
	$perso = !in_array('squelettes', $rep_exclus)
		? (strlen($GLOBALS['dossier_squelettes']) ? explode(':', $GLOBALS['dossier_squelettes']) : array('squelettes'))
		: array();
	if ($perso) {
		foreach($perso as $_rep) {
			if (is_dir(_DIR_RACINE . $_rep))
				$rep_perso[] = $_rep;
		}
	}
	// Recuperation des repertoires SPIP
	$rep_spip = array();
	if (!in_array('ecrire', $rep_exclus)) {
		$rep_spip[] = rtrim(_DIR_RESTREINT_ABS, '/');
	}
	if (!in_array('prive', $rep_exclus)) {
		$rep_spip[] = 'prive';
	}
	if (!in_array('squelettes_dist', $rep_exclus)) {
		$rep_spip[] = 'squelettes-dist';
	}
	$rep_scan = array_merge($rep_perso, $rep_plugins, $rep_suppl, $rep_extensions, $rep_spip);

	// construction des <select>
	// -- les fichiers de langue
	$sel_lang = '<select name="fichier_langue" id="fichier_langue" style="margin-bottom:1em;">'."\n";
	$sel_lang .= '<option value="0"';
	$sel_lang .= ($sel_l == '0') ? ' selected="selected">' : '>';
	$sel_lang .= _T('langonet:option_aucun_fichier') . '</option>' . "\n";
	// -- les racines des arborescences a scanner
	if ($multiple) {
		$sel_dossier = '<select name="dossier_scan[]" id="dossier_scan" multiple="multiple">' . "\n";
	}
	else {
		$sel_dossier = '<select name="dossier_scan" id="dossier_scan">' . "\n";
		$sel_dossier .= '<option value="0"';
		$sel_dossier .= (count($sel_d) == 0) ? ' selected="selected">' : '>';
		$sel_dossier .= _T('langonet:option_aucun_dossier') . '</option>' . "\n";
	}

	// la liste des options :
	// value (fichier_langue) =>
	//     $rep (nom du repertoire parent de lang/)
	//     $module (prefixe fichier de langue)
	//     $langue (index nom de langue)
	//     $ou_lang (chemin relatif vers fichier de langue a verifier)
	foreach ($rep_scan as $_rep) {
		if (in_array($_rep, $rep_plugins)) {
			$reel_dir = _DIR_PLUGINS . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($_rep, $rep_extensions)) {
			$reel_dir = _DIR_PLUGINS_DIST . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($_rep, $rep_suppl)) {
			$reel_dir = _DIR_PLUGINS_SUPPL . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else {
			$reel_dir = _DIR_RACINE . $_rep;
			$ou_fichier = $_rep . '/';
		}

		// on recupere tous les fichiers de langue directement places
		// dans lang/ sans parcourir d'eventuels sous-repertoires. On exclut si demandé ou par défaut
		// les fichiers de langue du paquet.xml
		if (is_dir($reel_dir . '/lang/')) {
			$opt_lang = '';
			foreach ($fic_lang = preg_files($reel_dir . '/lang/', _LANGONET_PATTERN_FICHIERS_LANG, 250, false) as $le_module) {
				preg_match_all(_LANGONET_PATTERN_CODE_LANGUE, str_replace('.php', '', $le_module), $matches);
				$module = str_replace($matches[0][0].'.php', '', $le_module);
				$module = str_replace($reel_dir . '/lang/', '', $module);
				if (!$exclure_paquet
				OR ($exclure_paquet	AND (strtolower(substr($module, 0, 7)) != 'paquet-'))) {
					$langue = ltrim($matches[0][0], '_');
					$ou_langue = str_replace('../', '', $reel_dir) . '/lang/';
					$value = $_rep.':'.$module.':'.$langue.':'.$ou_langue;
					$opt_lang .= '<option value="' . $value;
					$opt_lang .= ($value == $sel_l) ? '" selected="selected">' : '">';
					$opt_lang .= str_replace('.php', '', str_replace($reel_dir . '/lang/', '', $le_module)) . '</option>' . "\n";
				}
			}
			if ($opt_lang) {
				$sel_lang .= '<optgroup label="' . str_replace('../', '', $reel_dir) . '/">' . "\n";
				$sel_lang .= $opt_lang;
				$sel_lang .= '</optgroup>' . "\n";
			}
		}
		$sel_dossier .= '<option value="' . $ou_fichier;
		$sel_dossier .= (in_array($ou_fichier,$sel_d)) ? '" selected="selected">' : '">';
		$sel_dossier .= str_replace('../', '', $reel_dir) . '/</option>' . "\n";
	}

	$sel_lang .= '</select>' . "\n";
	$sel_dossier .= '</select>' . "\n";

	return $retour = array('fichiers' => $sel_lang, 'dossiers' => $sel_dossier);
}


/**
 * Lister tous les plugins contenus dans une arborescence donnée.
 *
 * @param string $racine_arborescence
 * @return array
 */
// $rep_base  => le repertoire de depart de l'arboresence a scanner
function lister_dossiers_plugins($racine_arborescence=null) {
	include_spip('inc/plugin');
	// liste_plugin_files() integre les repertoires supplementaires de plugins
	// dans le cadre de la mutualisation
	$liste_dossiers = liste_plugin_files($racine_arborescence);
	if (is_null($racine_arborescence))
		$racine_arborescence = _DIR_PLUGINS;
	$dossiers = array();
	foreach ($liste_dossiers as $_dossier) {
		$chemin = $racine_arborescence . $_dossier;
		$dossiers[] = $_dossier;
		if ($liste_sous_dossiers = glob($chemin . '/*/lang', GLOB_ONLYDIR)) {
			for ($i = 0; $i < count($liste_sous_dossiers); $i++) {
    			$dossiers[] = str_replace($racine_arborescence, '', str_replace('/lang', '', $liste_sous_dossiers[$i]));
			}
		}
	}
	return $dossiers;
}


function langonet_lister_operations() {

	// Initialisation en static pour les performances
	static $operations = array();

	if (!$operations) {
		// Les index désignent les groupes de fonctions, le tableau associé contient noms des opérations
		// telles qu'utilisés dans l'URL de la page de Langonet.
		$operations = array(
			'verifications' => array('verifier_item', 'verifier_l', 'verifier_doublon'),
			'recherches'    => array('rechercher_item', 'rechercher_texte', 'lister'),
			'traductions'   => array('generer', 'editer', 'traduire')
		);

		// On complète par des collections fournies par d'autres plugin
		$operations = pipeline('declarer_operations_langonet', $operations);
	}

	return $operations;
}
