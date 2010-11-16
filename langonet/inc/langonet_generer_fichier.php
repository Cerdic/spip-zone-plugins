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
	$resultats = array();

	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue_source;
	if (empty($GLOBALS[$var_source])) {
		$GLOBALS['idx_lang'] = $var_source;
		if ( file_exists($source = _DIR_RACINE.$ou_langue.$module.'_'.$langue_source.'.php'))
			include($source);
		else {
			$resultats['message_erreur'] = _T('langonet:message_nok_fichier_langue', 
										array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue));
			return $resultats;
		}
	}
	
	$var_cible = "i18n_".$module."_".$langue_cible;
	if (empty($GLOBALS[$var_cible])) {
		$GLOBALS['idx_lang'] = $var_cible;
		if ( file_exists($cible = _DIR_RACINE.$ou_langue.$module.'_'.$langue_cible.'.php'))
			include($cible);
	}

	$dir = sous_repertoire(_DIR_TMP,"langonet");
	$dir = sous_repertoire($dir,"generation");
	$f = $dir . $module . "_".$langue_cible . ".php";

	$i = 0;
	$initiale = '';
	$texte = '';
	// On recupere les items du fichier de langue si celui ci n'est pas vide
	$source = $GLOBALS[$var_source];
	$source = (!$source) ? array() : $source;
	// Si on demande de generer le fichier corrige alors on fournit la liste des items a ajouter
	$source = ($mode == 'oublie') ? array_merge($source, $oublis_inutiles) : $source;
	// On range les items dans l'ordre alphabetique
	if ($source) 
		ksort($source);
	foreach ($source as $_item => $_valeur) {
		$i++;
		if ($initiale != strtoupper(substr($_item, 0, 1))) {
			$texte .= "\n// " . strtoupper(substr($_item, 0, 1)) . "\n";
			$initiale = strtoupper(substr($_item, 0, 1));
		}
		$valeur_cible = $GLOBALS[$var_cible][$_item];
		if ($GLOBALS[$var_cible][$_item]) {
			$definition = "\t'" . $_item . "' => '" . addslashes($valeur_cible) . "',\n";
			if (($mode == 'inutile') AND in_array($_item, $oublis_inutiles))
				$texte .= "/*\t<LANGONET_DEFINITION_OBSOLETE>\n" . $definition . "*/\n";
			else
				$texte .= $definition;
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
				$texte .= "\t'" . $_item . "' => '" . $valeur_cible . "',\n";
			}
		}
	}
	
	$date = date('d-m-Y H:i:s');
	$texte = '<?php
// Ceci est un fichier langue de SPIP -- This is a SPIP language file
// Produit automatiquement par le plugin LangOnet a partir de la langue source ' . $langue_source . '
// Module: ' . $module . '
// Langue: ' . $langue_cible . '
// Date: ' . $date . '
// Items: ' . $i . '

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
'. $texte .'
);
?>';

	if ($encodage == 'utf8') {
		include_spip('inc/langonet_utils');
		$texte = entite2utf($texte);
	}
	
	$ok = ecrire_fichier($f, $texte);

	if (!$ok) {
		$resultats['message_erreur'] = _T('langonet:message_nok_ecriture_fichier', 
										array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['fichier'] = $f;
		$resultats['message_ok'] = _T('langonet:message_ok_fichier_genere', 
									array('langue' => $langue_cible, 'module' => $module, 'fichier' => $f));
	}
	return $resultats;
}
?>