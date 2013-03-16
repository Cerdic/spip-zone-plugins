<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_LANGONET_SIGNATURE', "// Ceci est un fichier langue de SPIP -- This is a SPIP language file");

define('_LANGONET_DEFINITION_L', '<LANGONET_DEFINITION_L>');
define('_LANGONET_DEFINITION_MANQUANTE', '<LANGONET_DEFINITION_MANQUANTE>');
define('_LANGONET_DEFINITION_OBSOLETE', '<LANGONET_DEFINITION_OBSOLETE>');

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
function inc_langonet_generer_fichier($module, $langue_source, $ou_langue, $langue_cible='en', $mode='index', $encodage='utf8', $oublis_inutiles=array()) {
	include_spip('inc/traduire');
	$bandeau = '';
	$var_source = "i18n_".$module."_".$langue_source;
	$source = _DIR_RACINE.$ou_langue.$module.'_'.$langue_source.'.php';
	// Trouver dans quel cas ce fichier n'a pas deja ete inclus a ce stade
	if (empty($GLOBALS[$var_source])) {
		if (!file_exists($source = _DIR_RACINE.$ou_langue.$module.'_'.$langue_source.'.php'))
			return array('message_erreur' =>  _T('langonet:message_nok_fichier_langue',  array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue)));

		$GLOBALS['idx_lang'] = $var_source;	
		include($source);
	}
	// Recuperer le bandeau d'origine si present.
	if ($file = file($source)) {
		array_shift($file); // saute < ? php
		foreach($file as $line) {
			$bandeau .= $line;
			if (strpos($line, _LANGONET_SIGNATURE) !== false)
			  {$file = ''; break;}
		}
		if ($file) $bandeau = '';
	}

	$var_cible = "i18n_".$module."_".$langue_cible;
	if (empty($GLOBALS[$var_cible])) {
		if (file_exists($cible = _DIR_RACINE.$ou_langue.$module.'_'.$langue_cible.'.php')) {
			$GLOBALS['idx_lang'] = $var_cible;
			include($cible);
		}
	}

	$source = langonet_generer_couples($module, $var_source, $var_cible, $mode, $encodage, $oublis_inutiles);

	$dir = sous_repertoire(_DIR_TMP,"langonet");
	$dir = sous_repertoire($dir,"generation");
	$bandeau .= "// Produit automatiquement par le plugin LangOnet a partir de la langue source $langue_source";
	$ok = ecrire_fichier_langue_php($dir, $langue_cible, $module, $source, $bandeau);

	if (!$ok) {
		$resultats['message_erreur'] = _T('langonet:message_nok_ecriture_fichier', array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['fichier'] = $ok;
		$resultats['message_ok'] = _T('langonet:message_ok_fichier_genere', array('langue' => $langue_cible, 'module' => $module, 'fichier' => $ok));
	}
	return $resultats;
}

function langonet_generer_couples($module, $var_source, $var_cible, $mode='index', $encodage='utf8', $oublis_inutiles=array()) {
	if ($encodage == 'utf8') include_spip('inc/langonet_utils');

	// On recupere les items du fichier de langue si celui ci n'est pas vide
	$source = $GLOBALS[$var_source] ? $GLOBALS[$var_source] : array();

	// Si on demande de generer le fichier corrige
	// alors on fournit la liste des items a ajouter
	$source = ($mode == 'oublie' OR $mode == 'fonction_l') ? array_merge($source, $oublis_inutiles) : $source;
	if ($mode != 'inutile') $oublis_inutiles = array();
	foreach ($source as $_item => $_valeur) {
		$texte = @$GLOBALS[$var_cible][$_item];
		if ($texte) {
			$comm = in_array($_item, $oublis_inutiles);
		}
		else {
			$comm = false;
			if ($mode != 'pas_item') {
				if ($mode == 'new')
					$texte = '<NEW>';
				else if ($mode == 'new_index')
					$texte = '<NEW>'.$_item;
				else if ($mode == 'new_valeur')
					$texte = '<NEW>'.$_valeur;
				else if ($mode == 'valeur')
					$texte = $_valeur;
				else if ($mode == 'vide')
					$texte = '';
				else if (($mode == 'fonction_l') OR (($mode == 'oublie') AND $_valeur))
					$texte = array(_LANGONET_DEFINITION_L, preg_replace("/'[$](\w+)'/", '\'@\1@\'', $_valeur), $mode);
				else if ($mode !== 'oublie')
					$texte = $_item;
				else if (preg_match('/^[a-z]+$/i', $_item))
					$texte = $_item;
				else $texte = _LANGONET_DEFINITION_MANQUANTE;
			}
		}
		if ($encodage == 'utf8') $texte = entite2utf($texte);
		$source[$_item] = $comm ? array(_LANGONET_DEFINITION_OBSOLETE, $texte, $mode) : $texte;
	}
	return $source;
}

// Produit un fichier de langue a partir d'un tableau (index => trad)
// Si trad n'est pas une chaine mais un tableau, on le met en commentaire

function produire_fichier_langue($langue, $module, $items, $producteur='')
{
	ksort($items);
	$initiale = '';
	$contenu = array();
	foreach($items as $k => $v) {
		if ($initiale != strtoupper($k[0])) {
			$initiale = strtoupper($k[0]);
			$contenu[]= "\n// $initiale";
		}
		if (!is_string($v)) {
			$t = str_replace("'", '\\\'', $v[1]);
			if ($v[2] == 'inutile')
				$contenu[]= "/*\t" . $v[0] ."\n\t'$k' => '$t',*/"; 
			else {
				$com = !$v[0] ? '' : ("/*\t". $v[0] ." */\n");
				$contenu[]= "$com\t'$k' => '$t',"; 
			}
		} else {
			$t = str_replace("'", '\\\'', $v);
			$t = str_replace('\\\\n', "' . \"\\n\" .'", $t);
			$contenu[]= "\t'$k' => '$t',";
		}
	}
	if (!strpos($producteur, _LANGONET_SIGNATURE)) 
		$producteur = "\n" . _LANGONET_SIGNATURE . "\n" . preg_replace(",\\n[/#]*,", "\n/// ", $producteur);

	return '<'. "?php\n" . $producteur . '
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

// Fonction aussi pour Plugonet
function ecrire_fichier_langue_php($dir, $langue, $module, $items, $producteur='')
{
	$nom = $dir . $module . "_" . $langue   . '.php';
	$contenu = produire_fichier_langue($langue, $module, $items, $producteur);
	return ecrire_fichier($nom, $contenu) ? $nom : false;
}
?>
