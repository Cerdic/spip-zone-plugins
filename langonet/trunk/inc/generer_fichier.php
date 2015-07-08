<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_SIGNATURE_SPIP'))
	define('_LANGONET_SIGNATURE_SPIP', "// This is a SPIP language file  --  Ceci est un fichier langue de SPIP");

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
function inc_generer_fichier($module, $langue_source, $ou_langue, $langue_cible='en', $mode='valeur', $encodage='utf8', $oublis_inutiles=array()) {

	// Modes correspondant à des corrections
	static $dossier_corrections = array('oublie' => 'definition', 'inutile' => 'utilisation', 'fonction_l' => 'fonction_l');

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On sauvegarde l'index de langue global si il existe car on va le modifier pendant le traitement.
	include_spip('inc/outiller');
	sauvegarder_index_langue_global();

	// Chargement du fichier de langue source (qui existe toujours) et du fichier de langue cible si il existe
	$langues = array('source' => $langue_source, 'cible' => $langue_cible);
	foreach ($langues as $_cle => $_langue) {
		list(${"traductions_${_cle}"}, ${"fichier_${_cle}"}) = charger_module_langue($module, $_langue, $ou_langue);
	}

	// Récupérer le bandeau d'origine si il existe.
	// Le bandeau est composé des lignes de commentaires avant le code
	$bandeau = '';
	if (file_exists($fichier_source)) {
		if ($tableau = file($fichier_source)) {
			array_shift($tableau); // saute < ? php
			foreach($tableau as $_ligne) {
				$_ligne = ltrim($_ligne);
				if ($_ligne) {
					if ((substr($_ligne, 0, 2) === '//')
					OR (substr($_ligne, 0, 1) === '#')) {
						$bandeau .= $_ligne;
					}
					else {
						break;
					}
				}
			}
		}
	}

	// Créer la liste des items du fichier cible sous la forme d'un tableau (raccourci, traduction)
	$items_cible = generer_items_cible($traductions_source, $traductions_cible, $mode, $encodage, $oublis_inutiles);

	// Ecriture du fichier de langue à partir de la liste des items cible
	$dossier_cible = sous_repertoire(_DIR_TMP,"langonet");
	if (isset($dossier_corrections[$mode])) {
		$dossier_cible = sous_repertoire($dossier_cible, "verification");
		$dossier_cible = sous_repertoire($dossier_cible, $dossier_corrections[$mode]);
	}
	else {
		$dossier_cible = sous_repertoire($dossier_cible, "generation");
	}
	$fichier_langue = ecrire_fichier_langue_php($dossier_cible, $langue_cible, $module, $items_cible, $bandeau, $langue_source);

	// On restaure l'index de langue global si besoin
	restaurer_index_langue_global();

	// On prepare le tableau des resultats
	if (!$fichier_langue) {
		$resultats['erreur'] = _T('langonet:message_nok_ecriture_fichier', array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['fichier'] = $fichier_langue;
	}

	return $resultats;
}


/**
 * @param array $idx_lang_source
 * @param array $idx_lang_cible
 * @param string $mode
 * @param string $encodage
 * @param array $oublis_inutiles
 * @return array
 */
function generer_items_cible($items_source, $items_cible, $mode='index', $encodage='utf8', $oublis_inutiles=array()) {

	// On distingue 3 cas de génération d'un fichier de langue cible :
	// 1- une génération d'une langue cible à partir d'une langue source (opération generer). Dans ce cas, aucun
	//    autre item que ceux de la langue source ne sont à considérer.
	// 2- correction de la langue source en ajoutant des items de langue (opéation fonction_l ou verifier_definition).
	//    Dans ce cas, le fichier cible correspondant à l'union des items source et des items à corriger.
	// 3- correction de la langue source en tagguant les items à supprimer (opération verifier_utilisation).
	//    Dans ce cas, les items cible coincident avec les items source et on conserve la liste des items inutiles à part.
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
		$texte = isset($items_cible[$_item]) ? $items_cible[$_item] : '';
		if ($texte) {
			if ($mode == 'inutile')
				$item_obsolete = array_key_exists($_item, $inutiles);
		}
		else {
			if ($mode == 'valeur') {
				$texte = _LANGONET_TAG_NOUVEAU . $_valeur;
			}
			else if ($mode == 'vide') {
				$texte = _LANGONET_TAG_NOUVEAU;
			}
			else if (($mode == 'fonction_l')) {
				$texte = array(
					_LANGONET_TAG_DEFINITION_L,
					preg_replace("/'[$](\w+)'/", '\'@\1@\'', $_valeur),
					$mode);
			}
			else if ($mode !== 'oublie') {
				$texte = _LANGONET_TAG_NOUVEAU . $_item;
			}
			else if (preg_match('/^[a-z]+$/i', $_item)) {
				$texte = $_item;
			}
			else {
				$texte = _LANGONET_TAG_DEFINITION_MANQUANTE;
			}
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
 * Ecriture d'un fichier de langue à partir de la liste de ces couples (item, traduction)
 * et de son bandeau d'information
 * Cette fonction est aussi utilisée par PlugOnet
 *
 * @param $dir
 * @param $langue
 * @param $module
 * @param $items
 * @param string $bandeau
 * @return bool|string
 */
function ecrire_fichier_langue_php($dir, $langue, $module, $items, $bandeau, $langue_source) {
	$nom_fichier = $dir . $module . "_" . $langue   . '.php';
	$contenu = produire_fichier_langue($langue, $module, $items, $bandeau, $langue_source);

	return ecrire_fichier($nom_fichier, $contenu) ? $nom_fichier : false;
}


/**
 * Produit un fichier de langue a partir d'un tableau (index => trad)
 * Si la traduction n'est pas une chaine mais un tableau, on inclut un commentaire
 *
 * @param $langue
 * @param $module
 * @param $items
 * @param string $bandeau
 * @return string
 */
function produire_fichier_langue($langue, $module, $items, $bandeau, $langue_source) {
	ksort($items);
	$initiale = '';
	$contenu = array();
	foreach($items as $_item => $_traduction) {
		if ($initiale != strtoupper($_item[0])) {
			$initiale = strtoupper($_item[0]);
			$contenu[]= "\n// $initiale";
		}
		if (!is_string($_traduction)) {
			$t = str_replace("\'", '\'', $_traduction[1]);
			$t = str_replace("'", '\\\'', $t);
			if ($_traduction[2] == 'inutile')
				$contenu[]= "/*\t" . $_traduction[0] ."\n\t'$_item' => '$t',*/";
			else {
				$prefixe = !$_traduction[0] ? '' : ("/*\t". $_traduction[0] ." */\n");
				$contenu[]= "${prefixe}\t'${_item}' => '${t}',";
			}
		}
		else {
			$t = str_replace("'", '\\\'', $_traduction);
			$t = str_replace('\\\\n', "' . \"\\n\" .'", $t);
			$t = str_replace(_LANGONET_TAG_NOUVEAU, '', $t, $c);
			$contenu[]= "\t'$_item' => '$t'," . ($c>0 ? ' ' . _LANGONET_TAG_NOUVEAU : '');
		}
	}

	if (strpos($bandeau, _LANGONET_SIGNATURE_SPIP) === false) {
		$bandeau = "\n" . _LANGONET_SIGNATURE_SPIP . "\n" . preg_replace(",\\n[/#]*,", "\n/// ", $bandeau);
	}

	return '<'. "?php\n" .
$bandeau . '
// Fichier produit par LangOnet à partir de la langue source ' . $langue_source . '
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
 * Conversion d'un texte en utf-8
 *
 * @param string	$texte
 * 		Texte à convertir en UTF-8
 *
 * @return string
 * 		Texte traduit en UTF-8 ou chaine vide
 *
 */
function entite2utf($texte) {
	$texte_utf8 = '';

	if ($texte AND is_string($texte)) {
		include_spip('inc/charsets');
		$texte_utf8 = unicode_to_utf_8(
			html_entity_decode(
				preg_replace('/&([lg]t;)/S', '&amp;\1', $texte),
				ENT_NOQUOTES,
				'utf-8')
		);
	}

	return $texte_utf8;
}

?>
