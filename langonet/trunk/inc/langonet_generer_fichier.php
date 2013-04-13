<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_SIGNATURE'))
	define('_LANGONET_SIGNATURE', "// Ceci est un fichier langue de SPIP -- This is a SPIP language file");

if (!defined('_LANGONET_DEFINITION_L'))
	define('_LANGONET_DEFINITION_L', '<LANGONET_DEFINITION_L>');
if (!defined('_LANGONET_DEFINITION_MANQUANTE'))
	define('_LANGONET_DEFINITION_MANQUANTE', '<LANGONET_DEFINITION_MANQUANTE>');
if (!defined('_LANGONET_DEFINITION_OBSOLETE'))
	define('_LANGONET_DEFINITION_OBSOLETE', '<LANGONET_DEFINITION_OBSOLETE>');

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

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On charge le fichier de langue source si il existe dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_source = "i18n_" . $module . "_" . $langue_source;
	$source = _DIR_RACINE . $ou_langue . $module . '_' . $langue_source . '.php';
	// Trouver dans quel cas ce fichier n'a pas deja ete inclus a ce stade
	if (empty($GLOBALS[$var_source])) {
		if (!file_exists($source))
			return array('erreur' =>  _T('langonet:message_nok_fichier_langue',  array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue)));
		$GLOBALS['idx_lang'] = $var_source;
		include($source);
	}

	// Récupérer le bandeau d'origine si il existe.
	// Le bandeau est composé des lignes qui précèdent la signature habituelle
	$bandeau = '';
	if ($tableau = file($source)) {
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
	$cible = _DIR_RACINE . $ou_langue . $module . '_' . $langue_cible . '.php';
	if (empty($GLOBALS[$var_cible])) {
		if (file_exists($cible)) {
			$GLOBALS['idx_lang'] = $var_cible;
			include($cible);
		}
	}

	// Créer la liste des items du fichier cible sous la forme d'un tableau (raccourci, traduction)
	$source = langonet_generer_items_cible($module, $var_source, $var_cible, $mode, $encodage, $oublis_inutiles);

	// Ecriture du fichier de langue à partir de la liste des items cible
	$dir = sous_repertoire(_DIR_TMP,"langonet");
	$dir = sous_repertoire($dir,"generation");
	$bandeau .= "// Produit automatiquement par le plugin LangOnet à partir de la langue source $langue_source";
	$ok = ecrire_fichier_langue_php($dir, $langue_cible, $module, $source, $bandeau);

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
function langonet_generer_items_cible($module, $var_source, $var_cible, $mode='index', $encodage='utf8', $oublis_inutiles=array()) {
	if ($encodage == 'utf8')
		include_spip('inc/langonet_utils');

	// On recupere les items du fichier de langue si celui ci n'est pas vide
	$source = $GLOBALS[$var_source] ? $GLOBALS[$var_source] : array();

	// Si on demande de generer le fichier corrige alors on fournit la liste des items à ajouter ou supprimer
	$source = ($mode == 'oublie' OR $mode == 'fonction_l') ? array_merge($source, $oublis_inutiles) : $source;
	if ($mode != 'inutile')
		$oublis_inutiles = array();

	foreach ($source as $_item => $_valeur) {
		$texte = @$GLOBALS[$var_cible][$_item];
		if ($texte) {
			$avec_commentaire = in_array($_item, $oublis_inutiles);
		}
		else {
			$avec_commentaire = false;
			if ($mode != 'pas_item') {
				if ($mode == 'valeur')
					$texte = _LANGONET_TAG_NOUVEAU . $_valeur;
				else if ($mode == 'vide')
					$texte = _LANGONET_TAG_NOUVEAU;
				else if (($mode == 'fonction_l') OR (($mode == 'oublie') AND $_valeur))
					$texte = array(_LANGONET_DEFINITION_L, preg_replace("/'[$](\w+)'/", '\'@\1@\'', $_valeur), $mode);
				else if ($mode !== 'oublie')
					$texte = _LANGONET_TAG_NOUVEAU . $_item;
				else if (preg_match('/^[a-z]+$/i', $_item))
					$texte = $_item;
				else $texte = _LANGONET_DEFINITION_MANQUANTE;
			}
		}
		if ($encodage == 'utf8')
			$texte = entite2utf($texte);
		$source[$_item] = $avec_commentaire ? array(_LANGONET_DEFINITION_OBSOLETE, $texte, $mode) : $texte;
	}

	return $source;
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
