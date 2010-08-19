<?php

if (!defined("_ECRIRE_INC_VERSION")) return; 

include_spip('inc/meta'); 

/* 
* Fonction d'installation, mise a jour de la base 
* 
* @param unknown_type $nom_meta_base_version 
 * @param unknown_type $version_cible 
 */ 
function spipbb_upgrade($nom_meta_base_version,$version_cible){ 
  include_spip('base/spipbb'); 
  include_spip('base/create'); 
  $current_version = 0.0; 
  if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){ 
	  if (version_compare($current_version,'1.0','<')) { 
	 	include_spip('base/soyezcreateurs'); 
	 	spip_log("spipBB installation 2.1", "spipbb_install"); 
	 	$groupe = create_groupe("spipBB", "Groupe permettant de sp&eacute;cifier les types de forums", "", 'non', 'non', 'oui', 'non', 'oui', 'non', 'non', 'oui', 'non', 'non'); 
	 	$ferme = create_mot("spipBB", "ferme", "Mettre ce mot clef aux sujets ferm&eacute;es", ""); 
	 	$annonce = create_mot("spipBB", "annonce", "Mettre ce mot clef aux annonce", ""); 
	 	$postit = create_mot("spipBB", "postit", "Mettre ce mot clef aux post-it", ""); 
	 	ecrire_config('spipbb/groupe_spipbb', $groupe); 
	 	ecrire_config('spipbb/mot_ferme', $ferme); 
	 	ecrire_config('spipbb/mot_annonce', $annonce); 
	 	ecrire_config('spipbb/mot_postit', $postit); 
	 	$secteur = create_rubrique('SpipBB Forums', 0, 'Les forums de spipBB dans cette rubrique'); 
	 	ecrire_config('spipbb/secteur_spipbb', $secteur); 
	 	ecrire_config('spipbb/utiliser_styliser', 'on'); 
	 	creer_base(); 
	 	ecrire_meta($nom_meta_base_version,$current_version='1.0','non');
	  }
  } 
} 

/* 
* Fonction de desinstallation 
* 
* @param unknown_type $nom_meta_base_version 
*/ 
function spipbb_vider_tables($nom_meta_base_version) { 
 	effacer_meta('spipbb'); 
 	effacer_meta($nom_meta_base_version); 
 	} 
?>
