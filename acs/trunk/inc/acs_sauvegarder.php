<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt
/**
 * Retourne le nom par défaut du fichier de sauvegarde
 * @return string filename
 */
function acs_nom_sauvegarde() {
	return lire_meta('acsSet').date("ymdHi", lire_meta("acsDerniereModif"));
}
/**
 * Sauvegarde de la configuration ACS du site
 * @param text $nom_sauvegarde
 * @return array
 */
function acs_sauvegarder($nom_sauvegarde) {
	$repertoire = _DIR_DUMP.'acs/';
	// si le répertoire n'existait pas, on le cree
	if (!is_writable($repertoire)) {
		if (!mkdir_recursive($repertoire)) {
			$err = _T('dump:avis_probleme_ecriture_fichier', array('fichier' => $repertoire));
			acs_log('inc/acs_sauvegarder : '.$err);
			return array('message_erreur'=> $err);
		}
	}
	include_spip('inc/composant/composants_variables');
	include_spip('inc/acs_version');
	$filename = $repertoire.$nom_sauvegarde.'.php';	
	$meta = $GLOBALS['meta'];
	foreach (liste_variables() as $vn=>$var) {
		$vn = 'acs'.$vn;
		if (isset($meta[$vn]))
			$file .= "'$vn'=>'".str_replace("'", "\'", $meta[$vn])."',\n";
	}
	if ($file) {
		$file = "<?php # backup of ".$meta['acsSet']."\n\$def=array(\n".
				"'ACS_VERSION'=>'".acs_version()."',\n".
				"'ACS_RELEASE'=>'".acs_release()."',\n".
				"'acsSet'=>'".$meta['acsSet']."',\n".
				($meta['acsSqueletteOverACS'] ? "'acsSqueletteOverACS'=>'".$meta['acsSqueletteOverACS']."',\n" : '').
				"'ACS_VOIR_ONGLET_VARS'=>'".$meta['ACS_VOIR_ONGLET_VARS']."',\n".
				"'ACS_VOIR_PAGES_COMPOSANTS'=>'".$meta['ACS_VOIR_PAGES_COMPOSANTS']."',\n".
				"'ACS_VOIR_PAGES_PREVIEW'=>'".$meta['ACS_VOIR_PAGES_PREVIEW']."',\n".
				$file.
				");\n?>";
		if (ecrire_fichier($filename, $file)) {
			acs_log('inc/acs_sauvegarder : '.$filename);
			return(array('message_ok' => _T('acs:sauvegarde_ok')));
		}
	}
	return(array('message_erreur' => _T('dump:erreur_taille_sauvegarde', array('fichier' => $filename))));	
}
?>