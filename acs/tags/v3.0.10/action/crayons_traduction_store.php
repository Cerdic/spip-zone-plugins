<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * Crayon pour une traduction ACS - Sauvegarde
 * Crayon for one ACS translation - Store changes
*/
function action_crayons_traduction_store_dist() {
  include_spip('inc/crayons');
  // Inclusion de l'action crayons_store du plugin crayons, comme librairie
  include_spip('action/crayons_store');

	lang_select($GLOBALS['auteur_session']['lang']);
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);
	  
  // Dernière sécurité :Accès réservé aux admins ACS
  // Last security: access restricted to ACS admins 
  if (!autoriser('acs', 'crayons_traduction_store')) {
  	echo crayons_var2js(array('$erreur' => _T('avis_operation_impossible')));
  	exit;
  }
  
	$wid = $_POST['crayons'][0];
	if (!verif_secu($_POST['name_'.$wid], $_POST['secu_'.$wid])) {
		acs_log('ACS action/crayons_traduction_store : verif_secu('.$_POST['name_'.$wid].', '.$_POST['secu_'.$wid].') returned false');	
    return false;
    exit;
  }  
	$champ = $_POST['fields_'.$wid];
	// On lit le composant et le nom de variable dans le champ, qui est de forme <composant>_<variable>
	if (preg_match('/\b([^_|^\W]+)_(\w+)\b/', $champ, $matches)) {
		$c = $matches[1];
		$var = $matches[2];
	}
	else {
		echo crayons_var2js(array('$erreur' => _U('crayons:donnees_mal_formatees').' (champ <> composant_variable)'));
		exit;
	}
	$oldval = $_POST['oldval_'.$wid];	
	$newval = $_POST[$champ.'_'.$wid];
	$cadre = $_POST['cadre_'.$wid];
	$lang = $_POST['lang_'.$wid];
	
	// On récupère le fichier de langue que trouve SPIP par find_in_path()
	$langfile = _DIR_COMPOSANTS.'/'.$c.(($cadre != '') ? '/'.$cadre : '').'/lang/'.$c.(($cadre != '') ? '_'.$cadre : '').'_'.$lang.'.php';
  if (!is_file($langfile)){
		echo crayons_var2js(array('$erreur' => _U('acs:err_fichier_absent', array('file' => $langfile))));
		exit;
	}
	$file = file_get_contents($langfile);
	$file = str_replace("'$var' => '$oldval'" , "'$var' => '$newval'", $file);
	if (!@file_put_contents($langfile, $file)) {
		echo crayons_var2js(array('$erreur' => _U('acs:err_fichier_ecrire', array('file' => $langfile))));
		exit;		
	}
	acs_log('ACS action/crayons_traduction_store '.$champ."=>".$newval. ' ('.$langfile.')');
	// Retourne la vue - Return vue 
	//$return['$erreur'] = 'Code pour faire ça pas encore écrit ... ;-)';
  $return[$wid] = $newval;
	echo crayons_var2js($return);
	exit;
}
?>