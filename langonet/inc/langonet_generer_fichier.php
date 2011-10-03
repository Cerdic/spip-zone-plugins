<?php
/// @file
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
	$var_source = "i18n_".$module."_".$langue_source;
	spip_log($GLOBALS[$var_source],'langonet');
	if (empty($GLOBALS[$var_source])) {
		if (!file_exists($source = _DIR_RACINE.$ou_langue.$module.'_'.$langue_source.'.php'))
			return array('message_erreur' =>  _T('langonet:message_nok_fichier_langue',  array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue)));
		include($source);
	}
	$GLOBALS['idx_lang'] = $var_source;	

	$var_cible = "i18n_".$module."_".$langue_cible;
	if (empty($GLOBALS[$var_cible])) {
		$GLOBALS['idx_lang'] = $var_cible;
		if ( file_exists($cible = _DIR_RACINE.$ou_langue.$module.'_'.$langue_cible.'.php'))
			include($cible);
	}

	$source = langonet_generer_couples($module, $var_source, $var_cible, $mode, $encodage, $oublis_inutiles);

	$dir = sous_repertoire(_DIR_TMP,"langonet");
	$dir = sous_repertoire($dir,"generation");
	$producteur = "Produit automatiquement par le plugin LangOnet a partir de la langue source $langue_source";
	$ok = ecrire_fichier_langue_php($dir, $langue_cible, $module, $source, $producteur);

	if (!$ok) {
		$resultats['message_erreur'] = _T('langonet:message_nok_ecriture_fichier', array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['fichier'] = $ok;
		$resultats['message_ok'] = _T('langonet:message_ok_fichier_genere', array('langue' => $langue_cible, 'module' => $module, 'fichier' => $ok));
	}
	return $resultats;
}

function langonet_generer_couples($module, $var_source, $var_cible, $mode='index', $encodage, $oublis_inutiles=array())
{
	if ($encodage == 'utf8') include_spip('inc/langonet_utils');

	// On recupere les items du fichier de langue si celui ci n'est pas vide
	$source = $GLOBALS[$var_source] ? $GLOBALS[$var_source] : array();
	// Si on demande de generer le fichier corrige
	// alors on fournit la liste des items a ajouter
	$source = ($mode == 'oublie') ? array_merge($source, $oublis_inutiles) : $source;
	if ($mode != 'inutile') $oublis_inutiles = array();
	foreach ($source as $_item => $_valeur) {
		$texte = $GLOBALS[$var_cible][$_item];
		$comm = false;
		if ($GLOBALS[$var_cible][$_item]) {
			$comm = in_array($_item, $oublis_inutiles);
		}
		else {
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
				else if ($mode == 'oublie')
					$texte = '<LANGONET_DEFINITION_MANQUANTE>';
				else
					$texte = $_item;
			}
		}
		if ($encodage == 'utf8') $texte = entite2utf($texte);
		$source[$_item] = $comm ? array("<LANGONET_DEFINITION_OBSOLETE>", $texte) : $texte;
	}
	return $source;
}

/// Produit un fichier de langues a partir d'un tableau (index => trad)
/// Si trad n'est pas une chaine mais un tableau, on le met en commentaire

function produire_fichier_langue($langue, $module, $items, $producteur='')
{
	ksort($items);
	$initiale = '';
	$contenu = array();
	foreach($items as $k => $v) {
		if ($initiale != strtoupper($k[0])) {
			$initiale = strtoupper($k[0]);
			$contenu[]= "// $initiale";
		}
		if (!is_string($v))
			$contenu[]= "/*\t" . $v[0] ."\n\t'" . $k . "' => '" . addslashes($v[1]) ."',*/\n"; 
		else {
			$v = addslashes($v);
			$v = str_replace('\\\\n', "' . \"\\n\" .'", $v);
			$contenu[]= "\t'" . $k . "' => '$v',";
		}
	}

	return '<'.'?php' . "\n" . '
/// @file
/// Ceci est un fichier langue de SPIP -- This is a SPIP language file' . '
/// ' . preg_replace(",\\n[/#]*,", "\n/// ", $producteur) . '
/// Module: ' . $module . '
/// Langue: ' . $langue . '
/// Date: ' . date('d-m-Y H:i:s') . '
/// Items: ' . count($items) . '

if (!defined(\'_ECRIRE_INC_VERSION\')) return;

$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
' .
	  join("\n", $contenu)  .
	  "\n);\n?".'>';
}

function ecrire_fichier_langue_php($dir, $langue, $module, $items, $producteur='')
{
	$nom = $dir . $module . "_" . $langue   . '.php';
	$c = produire_fichier_langue($langue, $module, $items, $producteur);
	return ecrire_fichier($nom, $c) ? $nom : false;
}
?>
