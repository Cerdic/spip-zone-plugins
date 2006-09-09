<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

// Fonction qui gere les habillages. Tentative de faire la fonction sans appel a la
// base de donnees, et donc sans manipulation sql.
function exec_config_habillages() {
  global $connect_statut, $connect_toutes_rubriques;

  include_spip("inc/presentation");
  include_spip ("base/abstract_sql");

  debut_page('&laquo; '._T('habillageprive:titre_page').' &raquo;', 'configurations', 'habillage_prive','',_DIR_PLUGIN_HABILLAGES.'/img_pack/habillage_prive.css');

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }

  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {

 	echo '<br><br>';
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/img_pack/habillage_prive-48.png">';
 	gros_titre(_T('habillageprive:gros_titre'));

 	barre_onglets("configuration", "config_habillages");

 	/*Affichage*/
 	debut_gauche();	
 	
 	debut_boite_info();
 	echo propre(_T('habillageprive:help'));
 	fin_boite_info();

 	debut_droite();

 	# Pour enlever le message d'avertissement, enlever le code a partir d'ici...
 	debut_boite_info();
	echo "<div class='verdana2' align='justify'>
	<p align='center'><B>"._T('avis_attention')."</B></p>",
	  http_img_pack("warning.gif", (_T('avis_attention')), "width='48' height='48' align='right' style='padding-$spip_lang_left: 10px;'");
	echo _T('habillageprive:texte_inc_config');
	echo "</div>";
	fin_boite_info();
 	# ... jusque la.
 	
	echo "<br />";
	
	debut_cadre_trait_couleur("", false, "", _T('habillageprive:titre_habillage_prive'));

		
 	echo '<form action="'.generer_url_ecrire('config_habillages').'" method="post">';
 	
 	// Debut des manipulations de mes_options.php. Le fichier mes_options sert de 
 	// reference pour savoir quel habillage a choisi l'utilisateur.
 	
 	$options_file = "mes_options.php";
 	$theme = $_REQUEST['theme'];
 	$plugin_directory = _DIR_PLUGIN_HABILLAGES;

 	# N'agir que si le theme n'est pas vide. Ceci permet, en cas de non-choix, de ne pas
 	# ecrire un fichier mes_options.php errone. 	
 	if ($theme != "") {
	 	// Si le fichier inc/mes_options.php existe deja
	 	if (file_exists($options_file)) {
			$backup_file = "$options_file.backup";
		 	rename($options_file, $backup_file);
		 	$open_backup_file = fopen($backup_file, 'r');
			$backup_file_size = filesize ($backup_file);
	 		$read_backup_file = fread ($open_backup_file, $backup_file_size);
	 		$search_comment = eregi("//start_define_img_pack(.*)//end_define_img_pack", $read_backup_file, $comment);
	 		$search_original_content = eregi("define\(\'_DIR_IMG_PACK\', \(\'(.*)\'\)\)\;", $read_backup_file, $original_content);
	 		$search_content = eregi("define\(\'_DIR_IMG_PACK\', \(\'(.*)\'\)\)\;(.*)//end_define_img_pack", $read_backup_file, $content);
	 		$search_all_content = eregi("<\?(.*)define\(\'_DIR_IMG_PACK\', \(\'(.*)\'\)\)\;(.*)\?>", $read_backup_file, $all_content);
		 	
		 	# Si l'utilisateur ou l'utilisatrice ne demande pas a revenir a la situation 
		 	# initiale (= a son chemin vers img_pack d'origine).
		 	if ($theme != "initial") {
	
			 	# Si le fichier ecrire/mes_options.php contient le commentaire ajoute par 
		 		# le plugin, cela signifie que le plugin a deja ete active pour un habillage.
		 		# Il faut donc modifier la ligne existante personnalisee du chemin vers 
		 		# img_pack. :
		 		if ($search_comment) {
			 		$open_options_file = fopen($options_file, 'w+');
			 		$new_content = $plugin_directory."/prive/themes/".$theme."/img_pack/";
			 		$insert_new_content = ereg_replace($content[1], $new_content, $read_backup_file);
			 		$write = fwrite($open_options_file, $insert_new_content);
			 		fclose($open_options_file);
		 		}
		 		
		 		# Si le fichier mes_options sauvegarde redefinissait le chemin d'img_pack
		 		# par la ligne define('_DIR_IMG_PACK', [...]) avant le choix d'un autre 
		 		# habillage :
		 		else if ($search_original_content) {
			 		$search_comment_backup = eregi("//backup(.*)", $read_backup_file);
			 		
			 		if ($search_comment_backup) {
				 		$open_options_file = fopen($options_file, 'w+');
				 		$new_content = "//start_define_img_pack\ndefine('_DIR_IMG_PACK', ('".$plugin_directory."/prive/themes/".$theme."/img_pack/'));\n//end_define_img_pack\n?>";
				 		$insert_new_content = ereg_replace( '\?>', $new_content, $read_backup_file);
				 		$write = fwrite($open_options_file, $insert_new_content);
				 		fclose($open_options_file);
			 		}
			 		else {
				 		$open_options_file = fopen($options_file, 'w+');
				 		$replaced_content = "define\(\'_DIR_IMG_PACK\', \(\'";
				 		$new_content = "//start_define_img_pack\ndefine('_DIR_IMG_PACK', ('".$plugin_directory."/prive/themes/".$theme."/img_pack/'));\n//end_define_img_pack\n//backup_define('_DIR_IMG_PACK', ('";
				 		$insert_new_content = ereg_replace( $replaced_content, $new_content, $read_backup_file);
				 		$write = fwrite($open_options_file, $insert_new_content);
				 		fclose($open_options_file);
		 			}
		 		}
		 		
		 		# Si le fichier ecrire/mes_options.php existe deja mais qu'il ne redefinie 
		 		# pas le chemin vers img_pack.
		 		else {
			 		$open_options_file = fopen($options_file, 'w+');
			 		$new_content = "//start_define_img_pack\ndefine('_DIR_IMG_PACK', ('".$plugin_directory."/prive/themes/".$theme."/img_pack/'));\n//end_define_img_pack\n?>";
			 		$insert_new_content = ereg_replace( '\?>', $new_content, $read_backup_file);
			 		$write = fwrite($open_options_file, $insert_new_content);
			 		fclose($open_options_file);
		 		}
		 		
	 		}
	 		
	 		# Si l'utilisateur ou l'utilisatrice veut revenir a la situation initiale.
	 		else if ($theme == "initial") {
		 		$search_comment_backup = eregi("//backup_define\(\'_DIR_IMG_PACK\', \(\'(.*)\'\)\)\;", $read_backup_file);
		 		
		 		if ($search_comment) {
			 		$open_options_file = fopen($options_file, 'w+');
			 		$erased_content = "//start_define_img_pack(.*)//end_define_img_pack";
			 		$insert_new_content = ereg_replace($erased_content, '', $read_backup_file);
			 		$write = fwrite($open_options_file, $insert_new_content);
			 		fclose($open_options_file);
		 		}
		 		
		 		else if ($search_comment_backup){
			 		$open_options_file = fopen($options_file, 'w+');
			 		$insert_new_content = ereg_replace( '//backup_define', 'define', $read_backup_file);
			 		$write = fwrite($open_options_file, $insert_new_content);
			 		fclose($open_options_file);
			 	}
			 	
		 		else {
			 		rename($backup_file, $options_file);
		 		}
	 		}
	 	}
	 	
	 	else {
		 	$open_options_file = fopen($options_file, 'w+');
		 	$new_content = "<?\n//start_define_img_pack\ndefine('_DIR_IMG_PACK', ('".$plugin_directory."/prive/themes/".$theme."/img_pack/'));\n//end_define_img_pack\n?>";
			$write = fwrite($open_options_file, $new_content);
			fclose($open_options_file);
	 	}
	}
 	
 	echo '<INPUT type=radio name="theme" value="initial"';
 		if ($_REQUEST['theme'] == "initial") {
	 		echo "checked";
 		}
 	echo ">";
 	echo "Revenir &agrave; l'habillage d'origine";
 	echo "<br />";
 	
 	$dossier = opendir (_DIR_PLUGIN_HABILLAGES.'/prive/themes/');
	while ($fichier = readdir ($dossier)) {
    	if ($fichier != "." && $fichier != "..") {
	    	echo '<INPUT type=radio name="theme" value="'.$fichier.'"';
	    	if ($_REQUEST['theme'] == "" AND file_exists($options_file)) {
		    	$open_options_file = fopen($options_file, 'r');
				$options_file_size = filesize ($options_file);
				$read_options_file = fread ($open_options_file, $options_file_size);
				$search_template_name = eregi("$plugin_directory/prive/themes/(.*)/img_pack/", $read_options_file, $template_name);
				if ($template_name[1] == $fichier) {
		    	echo " checked";
	    		}
	    	}
	    	else if ($_REQUEST['theme'] == $fichier) {
		    	echo " checked";
	    	}
	    	echo ">";
        	echo $fichier.'<br />';
    	}
	}
	closedir ($dossier);
	
	echo '<input type="submit" value="'._T('valider').'"/>';
 	echo '</form>';
 	fin_cadre_trait_couleur();
 	
 	echo "<br />";
	
	debut_cadre_trait_couleur("", false, "", _T('habillageprive:titre_habillage_public'));
 	echo "A venir...";
	fin_cadre_trait_couleur();
  } 
  
  fin_page();
  
}

?>