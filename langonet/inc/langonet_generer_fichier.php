<?php
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
function inc_langonet_generer_fichier($module, $langue_source, $ou_langue, $langue_cible='en', $mode='index', $encodage='html', $oublis_inutiles=array()) {

	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue_source;
	if (empty($GLOBALS[$var_source])) {
		if (!file_exists($source = _DIR_RACINE.$ou_langue.$module.'_'.$langue_source.'.php'))
			return array('message_erreur' =>  _T('langonet:message_nok_fichier_langue',  array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue)));
	}
	$GLOBALS['idx_lang'] = $var_source;
	include($source);

	$var_cible = "i18n_".$module."_".$langue_cible;
	if (empty($GLOBALS[$var_cible])) {
		$GLOBALS['idx_lang'] = $var_cible;
		if ( file_exists($cible = _DIR_RACINE.$ou_langue.$module.'_'.$langue_cible.'.php'))
			include($cible);
	}

	$source = langonet_generer_couples($module, $var_source, $var_cible, $mode, $encodage, $oublis_inutiles);

	$dir = sous_repertoire(_DIR_TMP,"langonet");
	$dir = sous_repertoire($dir,"generation");
	$comm = "\n// Produit automatiquement par le plugin LangOnet a partir de la langue source $langue_source\n";
	$ok = ecrire_fichier_langue_php($dir, $langue_cible, $module, $source, $comm);

	if (!$ok) {
		$resultats['message_erreur'] = _T('langonet:message_nok_ecriture_fichier', array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['fichier'] = $ok;
		$resultats['message_ok'] = _T('langonet:message_ok_fichier_genere', array('langue' => $langue_cible, 'module' => $module, 'fichier' => $ok));
	}
	return $resultats;
}

function langonet_generer_couples($module, $var_source, $var_cible, $mode='index', $oublis_inutiles=array())
{
	if ($encodage == 'utf8') include_spip('inc/langonet_utils');

	// On recupere les items du fichier de langue si celui ci n'est pas vide
	$source = $GLOBALS[$var_source] ? $GLOBALS[$var_source] : array();
	// Si on demande de generer le fichier corrige alors on fournit la liste des items a ajouter
	$source = ($mode == 'oublie') ? array_merge($source, $oublis_inutiles) : $source;
	if ($mode != 'inutile') $oublis_inutiles = array();
	foreach ($source as $_item => $_valeur) {
		$valeur_cible = $GLOBALS[$var_cible][$_item];
		$comm = false;
		if ($GLOBALS[$var_cible][$_item]) {
			$texte = "'" . addslashes($valeur_cible) . "'";
			$comm = ($oublis_inutiles AND in_array($_item, $oublis_inutiles));
		}
		else {
			if ($mode != 'pas_item') {
				if ($mode == 'new')
					$valeur_cible = '<NEW>';
				else if ($mode == 'new_index')
					$valeur_cible = '<NEW>'.$_item;
				else if ($mode == 'new_valeur')
					$valeur_cible = '<NEW>'.addslashes($_valeur);
				else if ($mode == 'valeur')
					$valeur_cible = addslashes($_valeur);
				else if ($mode == 'vide')
					$valeur_cible = '';
				else if ($mode == 'oublie')
					$valeur_cible = '<LANGONET_DEFINITION_MANQUANTE>';
				else
					$valeur_cible = $_item;
				$texte = "'" . $valeur_cible . "'";
			}
		}
		if ($encodage == 'utf8') $texte = entite2utf($texte);
		$source[$_item] = "\n\t'" . $_item . "' => $texte,";
		if ($comm) $source[$_item] = "\n/*\t<LANGONET_DEFINITION_OBSOLETE>" . $source[$_item]  . "*/\n";
	}
	return $source;
}

function ecrire_fichier_langue_php($dir, $langue, $module, $items, $comm='')
{
	$contenu = '<'.'?php' . "\n" . '
// Ceci est un fichier langue de SPIP -- This is a SPIP language file'
. $comm . '
// Module: ' . $module . '
// Langue: ' . $langue . '
// Date: ' . date('d-m-Y H:i:s') . '
// Items: ' . count($items) . '

if (defined(\'_ECRIRE_INC_VERSION\')) {

$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
';
	ksort($items);
	$initiale = '';
	foreach($items as $k => $v) {
		if ($initiale != strtoupper($k[0])) {
			$initiale = strtoupper($k[0]);
			$contenu .= "\n// $initiale\n";
		}
		$contenu .= $v;
	}
	$contenu .= "\n);\n}\n?".'>';

	$nom = $dir . $module . "_" . $langue   . '.php';
	return ecrire_fichier($nom, $contenu) ? $nom : false;
}
?>