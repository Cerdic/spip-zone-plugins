<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

$acs_install_version = '0.7';

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
	// Initialisation des variables ACS
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
  $keys = array();
  include_spip('lib/composant/composant_liste');  
  foreach(composants_liste() as $class=>$tag) {
  	echo "<br />$class";
  	$install_dir = find_in_path('composants/'.$class.'/install');
  	if (!$install_dir) {
  		continue;
  	}
  	// installation des images du composant
  	if (is_readable($install_dir.'/IMG')) {
			copy_dir($install_dir.'/IMG/', '../'.$GLOBALS['ACS_CHEMIN'].'/');
			spip_log('ACS init : images du composant '.$class.' installées dans '.$GLOBALS['ACS_CHEMIN'].'/ depuis '.$install_dir);
			echo ", images";
  	}
  	
		// lecture des mots-clefs du composant
		if (is_readable($install_dir.'/keywords/'.$class.'_keywords_'.$GLOBALS['lang'].'.php'))
			$keyfile = $install_dir.'/keywords/'.$class.'_keywords_'.$GLOBALS['lang'].'.php';
		else
			$keyfile = $install_dir.'/keywords/'.$class.'_keywords_fr.php';
		// Si on ne trouve pas de mot-clés, on passe au composant suivant			
		if (!is_readable($keyfile)) {
			if (is_readable($install_dir.'/keywords/'))
				spip_log('ACS init : échec de lecture du fichier des mots-clefs du composant '.$class. ' : '.$keyfile);
			continue;
		}
		// On lit les mots-clefs du composant
		require_once($keyfile);
		if (!is_array($keywords)) {
			spip_log('ACS init : échec de lecture des mots-clefs du composant '.$class. ' depuis '.$keyfile);
			continue;
		}
		$keys = array_merge_recursive($keys, $keywords);
		echo ', '._T('mots_clefs');
  }
  // Installation des mots-clefs des composants
  acs_install_keywords($keys);
  echo '<br />';
}

/**
 * Installe les mots-clés définis dans le tableau passé en paramètre
 */
function acs_install_keywords($keywords) {
  if (!is_array($keywords) || (count($keywords) == 0))
  	return false;

  // On teste l'existence des deux tables des mots-clefs
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table_desc_groupes_mots = $trouver_table('groupes_mots');
	$table_desc_mots = $trouver_table('mots');
	if (!is_array($table_desc_groupes_mots) || (!is_array($table_desc_mots)))
		return false;
		
	// On récupère les noms des tables
	$table_groupes_mots = $table_desc_groupes_mots['table'];
	$table_mots = $table_desc_mots['table'];
	
	// On construit la requête SQL
	foreach($keywords as $rubtitre=>$keygroup) {
		// On recupere la clef primaire
		$id_groupe = sql_getfetsel('id_groupe', $table_groupes_mots, 'titre="'.$rubtitre.'"');
		if (!$id_groupe) {
			$r = sql_insertq($table_groupes_mots, array(
				'titre' => $rubtitre,
				'descriptif' => is_array($keygroup['descriptif']) ? $keygroup['descriptif'][0] : $keygroup['descriptif'],
  			'texte' => is_array($keygroup['texte']) ? $keygroup['texte'][0] : $keygroup['texte'],
  			'unseul' => is_array($keygroup['unseul']) ? $keygroup['unseul'][0] : $keygroup['unseul'],
  			'obligatoire' => is_array($keygroup['obligatoire']) ? $keygroup['obligatoire'][0] : $keygroup['obligatoire'],
  			'tables_liees' => is_array($keygroup['tables_liees']) ? $keygroup['tables_liees'][0] : $keygroup['tables_liees'],
				'minirezo' => is_array($keygroup['minirezo']) ? $keygroup['minirezo'][0] : $keygroup['minirezo'],
				'comite' => is_array($keygroup['comite']) ? $keygroup['comite'][0] : $keygroup['comite'],
				'forum' => is_array($keygroup['forum']) ? $keygroup['forum'][0] : $keygroup['forum'],
			));
			if ($r)
				$id_groupe = $r;
			// a ce stade, si l'on a pas d'id_groupe, c'est qu'il n'existait pas ET n'a pas pu etre cree
			if (!$id_groupe) {
				spip_log('ACS init : échec de création du groupe de mots-clés '.$keygroup['titre']);
				continue;
			}
			foreach($keygroup['mots'] as $mottitre=>$mots) {
				$id_mot = sql_getfetsel('id_mot', $table_mots, 'titre="'.$mottitre.'"');
				if (!$id_mot) {
    			$r = sql_insertq($table_mots, array(
    				'titre' => $mottitre,
    				'descriptif' => $mots['descriptif'],
      			'texte' => $mots['texte'],
    				'id_groupe' => $id_groupe,
    				'type' => $rubtitre));
    			if (!$r) {
    				spip_log('ACS init : échec de création du mot-clé '.$mots['titre']);
    				continue;
    			}
  			}
			}
		}
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