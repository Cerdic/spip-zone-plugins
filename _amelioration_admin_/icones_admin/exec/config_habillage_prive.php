<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_HABILLAGE_PRIVE',(_DIR_PLUGINS.end($p)));

// Fonction qui gere les habillages. Tentative de faire la fonction sans appel a la
// base de donnees, et donc sans manipulation sql.
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
	echo '<img src="' . _DIR_PLUGIN_HABILLAGE_PRIVE. '/img_pack/habillage_prive-48.png">';
 	gros_titre(_T('habillageprive:gros_titre'));

 	barre_onglets("configuration", "config_habillage_prive");

 	/*Affichage*/
 	debut_gauche();	
 	
 	debut_boite_info();
 	echo propre(_T('habillageprive:help'));
 	fin_boite_info();

 	debut_droite();

 	echo '<form action="'.generer_url_ecrire('config_habillage_prive').'" method="post">';
 	
 	// Debut des manipulations de mes_options.php. Le fichier mes_options sert de 
 	// reference pour savoir quel habillage a choisi l'utilisateur.
 	
 	$options_file = "mes_options.php";
 	$theme = $_REQUEST['theme'];
 	$plugin_directory = _DIR_PLUGIN_HABILLAGE_PRIVE;

 	
 	// Si le fichier mes_options.php existe,
 	if (file_exists($options_file)) {
// 	 	$backup_number = date(YmdHi);
// 		$backup_file = "$options_file$backup_number.backup";
		$backup_file = "$options_file.backup";
	 	rename($options_file, $backup_file);
	 		
		if ($theme != "initial") {
			$open_backup_file = fopen($backup_file, 'r');
			$backup_file_size = filesize ($backup_file);
	 		$read_backup_file = fread ($open_backup_file, $backup_file_size);
	 		$search_comment = eregi("//start_define_img_pack(.*)//end_define_img_pack", $read_backup_file);
	 		$search_content = eregi("define\(\'_DIR_IMG_PACK\', \(\'(.*)\'\)\)\;", $read_backup_file, $content);
	 		
	 		if ($search_comment) {
		 		$open_options_file = fopen($options_file, 'w+');
		 		$new_content = "".$plugin_directory."/themes/".$theme."/img_pack/";
		 		$insert_new_content = ereg_replace($content[1], $new_content, $read_backup_file);
		 		$write = fwrite($open_options_file, $insert_new_content);
	 		}
 		}
 	}
 	
 	else {
	 	$open_options_file = fopen($options_file, 'w+');
	 	$new_content = "<?\ndefine('_DIR_IMG_PACK', ('".$plugin_directory."/themes/".$theme."/img_pack/'));\n?>";
		$write = fwrite($open_options_file, $new_content);
 	}
 	
 	echo '<INPUT type=radio name="theme" value="initial"';
 		if ($_REQUEST['theme'] == "initial") {
	 		echo "checked";
 		}
 	echo ">";
 	echo "SPIP classique";
 	echo "<br />";
 	
 	$dossier = opendir (_DIR_PLUGIN_HABILLAGE_PRIVE.'/themes/');
	while ($fichier = readdir ($dossier)) {
    	if ($fichier != "." && $fichier != "..") {
	    	echo '<INPUT type=radio name="theme" value="'.$fichier.'"';
	    	if ($_REQUEST['theme'] == $fichier) {
		    	echo " checked";
	    	}
	    	echo ">";
        	echo $fichier.'<br />';
    	}
	}
	closedir ($dossier);
	
	echo '<input type="submit" value="'._T('valider').'"/>';
 	echo '</form>';
  } 
  
  fin_page();
  
}

?>