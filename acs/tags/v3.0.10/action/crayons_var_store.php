<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * Crayon pour une variable ACS - Sauvegarde
 * Crayon for one ACS variable - Store changes
*/
function action_crayons_var_store_dist() {
  include_spip('inc/crayons');
  // Inclusion de l'action crayons_store du plugin crayons, comme librairie
  include_spip('action/crayons_store');
  // Inclusion de la definition de la vue d'une variable
  include_spip('inc/acs_vars');
  include_spip('inc/texte'); // pour fonction couper() utilisee dans acs_vars
  
	lang_select($GLOBALS['auteur_session']['lang']);
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);
	  
  // Dernière sécurité :Accès réservé aux admins ACS
  // Last security: access restricted to ACS admins 
  if (!autoriser('acs', 'crayons_var_store')) {
  	echo crayons_var2js(array('$erreur' => _T('avis_operation_impossible')));
  	exit;
  }

	$wid = $_POST['crayons'][0];
	if (!verif_secu($_POST['name_'.$wid], $_POST['secu_'.$wid])) {
		acs_log('action/crayons_var_store : verif_secu('.$_POST['name_'.$wid].', '.$_POST['secu_'.$wid].') returned false');	
    return false;
    exit;
  }
  $name = $_POST['name_'.$wid];
  $name = explode('-', $name);
  $nic = $name[2];
	$var = $_POST['fields_'.$wid];
	$v = explode('_', $var);
	$c = $v[0]; // composant
	$v = $v[1]; // variable
	$var = 'acs'.ucfirst($c).($nic ? $nic : '').$v;
	$oldval = $_POST['oldval_'.$wid];
	// est-ce que la variable a ete modifiée entre-temps ?
	if ($oldval != $GLOBALS['meta'][$var]) {
    echo crayons_var2js(array('$erreur' => _U('crayons:modifie_par_ailleurs')));
    exit;
  }
  $type = $_POST['type_'.$wid];
  switch($type) {
    case 'bord':
    	$newColor = $_POST[$var.'Color_'.$wid];
    	$newStyle = $_POST[$var.'Style_'.$wid];
    	$newWidth = $_POST[$var.'Width_'.$wid];
    	$newval = serialize(array('Color' => $newColor, 'Style' => $newStyle, 'Width' => $newWidth));
    	break;
    case 'key':
    	$newGroup = $_POST[$var.'Group_'.$wid];
    	$newKey = $_POST[$var.'Key_'.$wid];
    	$newval = serialize(array('Group' => $newGroup, 'Key' => $newKey));
    	break;
    default:
    	$newval = $_POST[$var.'_'.$wid];
  }	
	ecrire_meta($var, $newval);
	ecrire_meta("acsDerniereModif", time()); // forcera un recalcul
	acs_log('ACS action/crayons_var_store '.$var."=>".$newval);
	// Retourne la vue - Return vue 
	$return['$erreur'] = NULL;
  $return[$wid] = affiche_variable($type, $var);
	echo crayons_var2js($return);
	exit;
}

?>