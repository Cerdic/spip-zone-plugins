<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGE_PRIVE',(_DIR_PLUGINS.end($p)));

function exec_config_habillage_prive() {
  global $connect_statut, $connect_toutes_rubriques;

  include_spip("inc/presentation");
  include_spip ("base/abstract_sql");

  debut_page('&laquo; '._T('habillageprive:titre_page').' &raquo;', 'configurations', 'habillage_prive','',_DIR_PLUGIN_HABILLAGE_PRIVE.'/img_pack/habillage_prive.css');

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }

  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {

 	echo '<br><br>';
	echo '<img src="' . _DIR_PLUGIN_HABILLAGE_PRIVE. '/../img_pack/habillage_prive-48.png">';
 	gros_titre(_T('habillageprive:gros_titre'));

 	barre_onglets("configuration", "config_habillage_prive");

 	/*Affichage*/
 	debut_gauche();	
 	
 	debut_boite_info();
 	echo propre(_T('habillageprive:help'));
 	fin_boite_info();

 	debut_droite();

 	echo '<form action="'.generer_url_ecrire('config_habillage_prive').'" method="post">';
	
 	echo '<INPUT type=radio name="theme" value="initial" checked>';
 	echo "Revenir &agrave; l'habillage de base<br />";
 	
 	$dossier = opendir (_DIR_PLUGIN_HABILLAGE_PRIVE.'/../themes/');
	while ($fichier = readdir ($dossier)) {
    	if ($fichier != "." && $fichier != "..") {
	    	echo '<INPUT type=radio name="theme" value="'.$fichier.'">';
        	echo $fichier.'<br />';
    	}
	}
	closedir ($dossier);
	
// 	$theme = $_REQUEST['theme'];
// 	echo $theme;
 	
 	echo '<input type="submit" value="'._T('valider').'"/>';
 	echo '</form>';
  } 
  
  fin_page();
  
}

?>