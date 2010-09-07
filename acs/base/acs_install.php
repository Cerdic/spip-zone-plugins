<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

$acs_install_version = '0.6';

function acs_install($action){
  switch ($action)
  {
    case 'test':
      return (isset($GLOBALS['meta']['acsInstalled']));
      break;

    case 'install':
      acs_set_default();
      break;

    case 'uninstall':
      acs_reset_vars();
      break;
  }
}


function acs_set_default() {
  $defaults = find_in_path('composants/def.php');
  if (is_readable($defaults))
    include $defaults;
  else 
    spip_log('ACS init failed : unable to read composants/def.php');  
  if (is_array($def)) {
    foreach($def as $var=>$value) {
      if (!isset($GLOBALS['meta'][$var]) || ($var=='acsModel'))
	      ecrire_meta($var, $value);
    }
    ecrire_meta('acsInstalled', $acs_install_version);
    ecrire_metas();
    lire_metas();
    spip_log('ACS init done with default values from composants/def.php');    
  }
  // Installation des composants
  include_spip('lib/composant/composant_liste');
  foreach(composants_liste() as $class=>$tag) {
  	$install_dir = find_in_path('composants/'.$class.'/install');
  	if (!$install_dir)
  		continue;
		copy_dir($install_dir.'/', '../'.$GLOBALS['ACS_CHEMIN'].'/');
  }
}

function acs_reset_vars() {
  spip_query("delete FROM spip_meta where left(nom,3)='acs'");
  lire_metas();
  spip_log('ACS variables DELETED');
  
}

// Copie recursive d'un dossier
function copy_dir($dir2copy, $dir_paste) {
// On verifie si $dir2copy existe et est un dossier
if ($dir2copy && @is_dir($dir2copy)) {
	// Si le dossier destination n'existe pas, on le cree
	if (!mkdir_recursive($dir_paste))
		return;
	if ($dh = opendir($dir2copy)) {
  	// On liste les dossiers et fichiers de $dir2copy
  	while (($file = readdir($dh)) !== false) {
  		if ($file=='.' || $file=='..' || substr($file, 0, 1)=='.')
  			continue; 
  		// S'il s'agit d'un dossier, on relance la fonction récursive
  		if(@is_dir($dir2copy.$file))
  			copy_dir($dir2copy.$file.'/', $dir_paste.$file.'/');
  		// S'il sagit d'un fichier, on le copie
  		else
  			copy($dir2copy.$file,$dir_paste.$file );
  	}
		// On ferme $dir2copy
		closedir($dh);
		}
	}
}
?>