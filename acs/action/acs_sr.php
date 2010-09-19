<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt



/**
 * Sauvegarde / restaure les variables ACS
 */
function action_acs_sr() {
	// renvoie "acs_sr : Accès interdit" en cas de tentative d'accès direct
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $securiser_action();

  acs_log('action_acs_sr'.dbg($_POST));
  
  $repertoire = _DIR_DUMP.'acs/';
  // si le répertoire n'existait pas, on le cree  
	if (!is_writable($repertoire)) {
		if (!mkdir_recursive($repertoire)) {
			acs_log('action/acs_sr : unable to create '.$repertoire);
			return false;
		}
	}

  // On récupère les paramètres
  $nom_sauvegarde = urldecode(_request('nom_sauvegarde'));
	
  if (_request('save') == 'go!')
  	acs_save($repertoire, $nom_sauvegarde);
  	
  if (_request('restore') == 'go!')
  	acs_restore();

}

// Sauvegarder
function acs_save($repertoire, $nom_fichier) {
	include_spip('lib/composant/composants_variables');
	$filename = $repertoire.$nom_fichier.'.php';
	
	$meta = $GLOBALS['meta'];
	foreach (liste_variables() as $vn=>$var) {
		$vn = 'acs'.$vn;
		if (isset($meta[$vn])) 
			$file .= "'$vn'=>'".str_replace("'", "\'", $meta[$vn])."',\n";
	}
	if ($file) {
		$file = "<?php # backup of ".$meta['acsModel']."\n\$def=array(\n".
			"'acsVersion'=>'".ACS_VERSION."',\n".
			"'acsRelease'=>'".ACS_RELEASE."',\n".
			"'acsModel'=>'".$meta['acsModel']."',\n".
			$file.
			");\n?>";
		ecrire_fichier($filename, $file);
	}
}

function acs_restore() {
	$loadvars = charger_fonction('acs_load_vars', 'inc');
	$repertoire = _DIR_DUMP.'acs/';
	$archive = $repertoire._request('archive').'.php';
	$r = $loadvars($archive);
	ecrire_meta('acsDerniereModif', time());
	acs_log('inc/acs_sr : restauré "'.$archive.'" '.$r);
}
?>