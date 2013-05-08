<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_SIGNATURE'))
	define('_LANGONET_SIGNATURE', "// Ceci est un fichier langue de SPIP -- This is a SPIP language file");

if (!defined('_LANGONET_TAG_DEFINITION_L'))
	define('_LANGONET_TAG_DEFINITION_L', '<LANGONET_DEFINITION_L>');
if (!defined('_LANGONET_TAG_DEFINITION_MANQUANTE'))
	define('_LANGONET_TAG_DEFINITION_MANQUANTE', '<LANGONET_DEFINITION_MANQUANTE>');
if (!defined('_LANGONET_TAG_DEFINITION_OBSOLETE'))
	define('_LANGONET_TAG_DEFINITION_OBSOLETE', '<LANGONET_DEFINITION_OBSOLETE>');

if (!defined('_LANGONET_TAG_NOUVEAU'))
	define('_LANGONET_TAG_NOUVEAU', '# NEW');
if (!defined('_LANGONET_TAG_MODIFIE'))
	define('_LANGONET_TAG_MODIFIE', '# MODIF');


/**
 * Ecriture des fichiers de langue
 * 
 * @param string $module
 * @param string $langue_source
 * @param string $ou_langue
 * @param string $langue_cible [optional]
 * @param string $mode [optional]
 * @param string $encodage [optional]
 * @param array $oublis_inutiles [optional]
 * @return 
 */
function inc_langonet_generer_fichier($module, $langue_source, $ou_langue, $langue_cible='en', $mode='valeur', $encodage='utf8', $oublis_inutiles=array()) {

	// Modes correspondant à des corrections
	static $dossier_corrections = array('oublie' => 'definition', 'inutile' => 'utilisation', 'fonction_l' => 'fonction_l');

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On charge le fichier de langue source si il existe dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_source = "i18n_" . $module . "_" . $langue_source;
	$fichier_source = _DIR_RACINE . $ou_langue . $module . '_' . $langue_source . '.php';
	// Trouver dans quel cas ce fichier n'a pas deja ete inclus a ce stade
	if (empty($GLOBALS[$var_source])) {
		if (file_exists($fichier_source)) {
			$GLOBALS['idx_lang'] = $var_source;
			include($fichier_source);
		}
		else
			if ($mode != 'fonction_l')
				return array('erreur' =>  _T('langonet:message_nok_fichier_langue',  array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue)));
	}

	// Récupérer le bandeau d'origine si il existe.
	// Le bandeau est composé des lignes qui précèdent la signature habituelle
	$bandeau = '';
	if ($tableau = file($fichier_source)) {
		array_shift($tableau); // saute < ? php
		$signature_trouvee = false;
		foreach($tableau as $_ligne) {
			$bandeau .= $_ligne;
			if (strpos($_ligne, _LANGONET_SIGNATURE) !== false) {
				$signature_trouvee = true;
				break;
			}
		}
		if (!$signature_trouvee)
			$bandeau = '';
	}

	// On charge le fichier de langue cible si il existe dans l'arborescence $ou_langue
	$var_cible = "i18n_" . $module . "_" . $langue_cible;
	$fichier_cible = _DIR_RACINE . $ou_langue . $module . '_' . $langue_cible . '.php';
	if (empty($GLOBALS[$var_cible])) {
		if (file_exists($fichier_cible)) {
			$GLOBALS['idx_lang'] = $var_cible;
			include($fichier_cible);
		}
	}

	// Créer la liste des items du fichier cible sous la forme d'un tableau (raccourci, traduction)
	$items_cible = langonet_generer_items_cible($var_source, $var_cible, $mode, $encodage, $oublis_inutiles);

	// Ecriture du fichier de langue à partir de la liste des items cible
	$dir = sous_repertoire(_DIR_TMP,"langonet");
	if (in_array($mode, $dossier_corrections)) {
		$dir = sous_repertoire($dir, "verification");
		$dir = sous_repertoire($dir, $dossier_corrections[$mode]);
	}
	else
		$dir = sous_repertoire($dir, "generation");
	$bandeau .= "// Produit automatiquement par le plugin LangOnet à partir de la langue source $langue_source";
	$ok = ecrire_fichier_langue_php($dir, $langue_cible, $module, $items_cible, $bandeau);

	if (!$ok) {
		$resultats['erreur'] = _T('langonet:message_nok_ecriture_fichier', array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['fichier'] = $ok;
	}

	return $resultats;
}


/**
 * @param string $module
 * @param array $var_source
 * @param array $var_cible
 * @param string $mode
 * @param string $encodage
 * @param array $oublis_inutiles
 * @return array
 */
function langonet_generer_items_cible($var_source, $var_cible, $mode='index', $encodage='utf8', $oublis_inutiles=array()) {
	if ($encodage == 'utf8')
		include_spip('inc/langonet_utils');

	// On distingue 3 cas de génération d'un fichier de langue cible :
	// 1- une génération d'une langue cible à partir d'une langue source (opération generer). Dans ce cas, aucun
	//    autre item que ceux de la langue source ne sont à considérer.
	// 2- correction de la langue source en ajoutant des items de langue (opéation fonction_l ou verifier_definition).
	//    Dans ce cas, le fichier cible correspondant à l'union des items source et des items à corriger.
	// 3- correction de la langue source en tagguant les items à supprimer (opération verifier_utilisation).
	//    Dans ce cas, les items cible coincident avec les items source et on conserve la liste des items inutiles à part.
	$items_source = $GLOBALS[$var_source] ? $GLOBALS[$var_source] : array();
	$inutiles = array();
	if ($mode == 'inutile')
		$inutiles = $oublis_inutiles; // cas 3
	else if ($mode == 'oublie' OR $mode == 'fonction_l')
		$items_source =  array_merge($items_source, $oublis_inutiles); // cas 2

	// On boucle sur la liste exacte des items cible pour affiner leur contenu suivant le type
	// d'opération en cours.
	foreach ($items_source as $_item => $_valeur) {
		// Si l'item existe dans le fichier cible existant on vérifie si il n'est pas obsolète dans le cas où
		// le mode est 'inutile' (opération verifier_utilisation)
		$item_obsolete = false;
		$texte = isset($GLOBALS[$var_cible][$_item]) ? $GLOBALS[$var_cible][$_item] : '';
		if ($texte) {
			if ($mode == 'inutile')
				$item_obsolete = in_array($_item, $inutiles);
		}
		else {
			if ($mode == 'valeur')
				$texte = _LANGONET_TAG_NOUVEAU . $_valeur;
			else if ($mode == 'vide')
				$texte = _LANGONET_TAG_NOUVEAU;
			else if (($mode == 'fonction_l') OR (($mode == 'oublie') AND $_valeur))
				$texte = array(_LANGONET_TAG_DEFINITION_L, preg_replace("/'[$](\w+)'/", '\'@\1@\'', $_valeur), $mode);
			else if ($mode !== 'oublie')
				$texte = _LANGONET_TAG_NOUVEAU . $_item;
			else if (preg_match('/^[a-z]+$/i', $_item))
				$texte = $_item;
			else $texte = _LANGONET_TAG_DEFINITION_MANQUANTE;
		}

		// Passage en utf8 et stockage du texte de l'item cible pour traitement ultérieur lors de l'écriture du fichier
		if ($encodage == 'utf8') {
			if (is_array($texte))
				$texte[1] = entite2utf($texte[1]);
			else
				$texte = entite2utf($texte);
		}
		$items_cible[$_item] = $item_obsolete ? array(_LANGONET_TAG_DEFINITION_OBSOLETE, $texte, $mode) : $texte;
	}

	return $items_cible;
}


/**
 * Produit un fichier de langue a partir d'un tableau (index => trad)
 * Si la traduction n'est pas une chaine mais un tableau, on inclut un commentaire
 *
 * @param $langue
 * @param $module
 * @param $items
 * @param string $producteur
 * @return string
 */
function produire_fichier_langue($langue, $module, $items, $producteur='') {
	ksort($items);
	$initiale = '';
	$contenu = array();
	foreach($items as $_item => $_traduction) {
		if ($initiale != strtoupper($_item[0])) {
			$initiale = strtoupper($_item[0]);
			$contenu[]= "\n// $initiale";
		}
		if (!is_string($_traduction)) {
			$t = str_replace("'", '\\\'', $_traduction[1]);
			if ($_traduction[2] == 'inutile')
				$contenu[]= "/*\t" . $_traduction[0] ."\n\t'$_item' => '$t',*/";
			else {
				$com = !$_traduction[0] ? '' : ("/*\t". $_traduction[0] ." */\n");
				$contenu[]= "$com\t'$_item' => '$t',";
			}
		}
		else {
			$t = str_replace("'", '\\\'', $_traduction);
			$t = str_replace('\\\\n', "' . \"\\n\" .'", $t);
			$t = str_replace(_LANGONET_TAG_NOUVEAU, '', $t, $c);
			$contenu[]= "\t'$_item' => '$t'," . ($c>0 ? ' ' . _LANGONET_TAG_NOUVEAU : '');
		}
	}
	if (!strpos($producteur, _LANGONET_SIGNATURE)) 
		$producteur = "\n" . _LANGONET_SIGNATURE . "\n" . preg_replace(",\\n[/#]*,", "\n/// ", $producteur);

	return '<'. "?php\n" .
$producteur . '
// Module: ' . $module . '
// Langue: ' . $langue . '
// Date: ' . date('d-m-Y H:i:s') . '
// Items: ' . count($items) . '

if (!defined(\'_ECRIRE_INC_VERSION\')) return;

$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
' .
	  join("\n", $contenu)  .
	  "\n);\n?".'>';
}


/**
 * Ecriture d'un fichier de langue à partir de la liste de ces couples (item, traduction)
 * et de son bandeau d'information
 * Cette fonction est aussi utilisée par PlugOnet
 *
 * @param $dir
 * @param $langue
 * @param $module
 * @param $items
 * @param string $producteur
 * @return bool|string
 */
function ecrire_fichier_langue_php($dir, $langue, $module, $items, $producteur='') {
	$nom_fichier = $dir . $module . "_" . $langue   . '.php';
	$contenu = produire_fichier_langue($langue, $module, $items, $producteur);

	return ecrire_fichier($nom_fichier, $contenu) ? $nom_fichier : false;
}
?>
