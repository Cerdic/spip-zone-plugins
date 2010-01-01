<?php
/**
 * Ecriture des fichiers de langue
 * 
 * @param string $module
 * @param string $langue_source
 * @param string $ou_langue
 * @param string $langue_cible [optional]
 * @param string $mode [optional]
 * @return 
 */
function inc_langonet_generer_fichier($module, $langue_source, $ou_langue, $langue_cible='en', $mode='index') {
	$resultats = array();

	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue_source;
	if (empty($GLOBALS[$var_source]))
		if (find_in_path($module.'_'.$langue_source.'.php',  $ou_langue))
			charger_langue($langue_source, $module);
		else {
			$resultats['statut'] = false;
			$resultats['erreur'] = _T('langonet:message_nok_fichier_langue', 
									array('langue' => $langue_source, 'module' => $module, 'dossier' => $ou_langue));
			return $resultats;
		}
	
	$var_cible = "i18n_".$module."_".$langue_cible;
	if (empty($GLOBALS[$var_cible]))
		if (find_in_path($module.'_'.$langue_cible.'.php', ou_langue))
			charger_langue($langue_cible, $module);
		else
			$GLOBALS[$var_cible] = array();

	$dir = sous_repertoire(_DIR_TMP,"langonet");
	$f = $dir . $module . "_".$langue_cible . ".php";

	$i = 0;
	$initiale = '';
	$texte = '';
	$source = $GLOBALS[$var_source];
	ksort($source);
	foreach ($source as $_item => $_valeur) {
		$i++;
		if ($initiale != strtoupper(substr($_item, 0, 1))) {
			$texte .= "\n// " . strtoupper(substr($_item, 0, 1)) . "\n";
			$initiale = strtoupper(substr($_item, 0, 1));
		}
		$valeur_cible = $GLOBALS[$var_cible][$_item];
		if ($GLOBALS[$var_cible][$_item]) {
			$texte .= "\t'" . $_item . "' => '" . addslashes($valeur_cible) . "',\n";
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
// Date: ' . $date . '
// Items: ' . $i . '

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
'. $texte .'
);
?>';
	$ok = ecrire_fichier($f, $texte);

	if (!ok) {
		$resultats['statut'] = false;
		$resultats['erreur'] = _T('langonet:message_nok_ecriture_fichier', 
								array('langue' => $langue_cible, 'module' => $module));
	}
	else {
		$resultats['statut'] = true;
		$resultats['erreur'] = _T('langonet:message_ok_fichier_genere', 
								array('langue' => $langue_cible, 'module' => $module, 'fichier' => $f));
	}
	return $resultats;
}
?>