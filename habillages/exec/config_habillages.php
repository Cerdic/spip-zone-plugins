<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',dirname(__FILE__)));
define('_DIR_PLUGIN_HABILLAGES_OPT',(_DIR_PLUGINS.end($p)));

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

#### DEBUT DE L'ENCADRE QUI GERE L'HABILLAGE PRIVE ######################################
	debut_cadre_trait_couleur("../"._DIR_PLUGIN_HABILLAGES."/img_pack/habillage_prive-32.png", false, "", _T('habillageprive:titre_habillage_prive'));
	
	echo "<small>La gestion des habillages de l'espace priv&eacute; est encore en d&eacute;veloppement. ";
	echo "Rien ne vous emp&ecirc;che de l'essayer (il faut juste savoir que les ic&ocirc;nes appara&icirc;tront au fur et &agrave; mesure";
	echo " de votre navigation dans l'espace priv&eacute; et que c'est un peu cahotique).</small><br />";
 	echo '<form action="'.generer_url_ecrire('config_habillages').'" method="post">';
 	
 	
 	$options_file = "mes_options.php";
 	$img_directory = "img_pack";
 	$plugin_directory = _DIR_PLUGIN_HABILLAGES;
 	$theme = $_REQUEST['theme'];
 	$theme_path = "$plugin_directory/prive/themes/$theme";
 	$theme_xml = "$theme_path/theme.xml";
 	$theme_duplicated = "img_pack/theme.xml";

 	# N'agir d'abord que si l'utilisateur a choisi un theme qui n'est pas initial et si le
 	# fichier mes_options.php. 	
 	if ($theme != "" AND $theme != "initial" AND file_exists($options_file)) {
	 	// Si le fichier inc/mes_options.php existe deja
		 	###echo "option existe";
		 	# Ouvrir et lire le fichier...
		 	$open_options_file = fopen($options_file, 'r');
			$options_file_size = filesize ($options_file);
	 		$read_options_file = fread ($open_options_file, $options_file_size);
	 		# ...definir les chaines recherchees...
	 		$search_comment_backup = eregi("//backup_define\(\'_DIR_IMG_PACK\', \(\'(.*)\'\)\)\;", $read_options_file, $comment_backup);
	 		$search_define = eregi("(.*)define\(\'_DIR_IMG_PACK\'(.*)", $read_options_file, $define_content);
	 		# ... et reagir en fonction de ce qui est trouve dans mes_options.php.
	 		
	 		if ($search_comment_backup) {
		 		###echo "commentaire backup trouve dans mes options";
		 		fclose($open_options_file);
		 		
		 		# Debut routine.
		 		if (file_exists($theme_duplicated) AND file_exists($theme_xml)) {
			 		###echo "theme.xml existe dans img_pack";
			 		$open_theme_xml = fopen($theme_xml, 'r');
					$theme_xml_size = filesize ($theme_xml);
					$read_theme_xml = fread ($open_theme_xml, $theme_xml_size);
					$search_theme_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_xml, $theme_name);
					$search_theme_version = eregi("<version>(.*)</version>", $read_theme_xml, $theme_version);
			 		
					$open_theme_duplicated = fopen($theme_duplicated, 'r');
					$theme_duplicated_size = filesize ($theme_duplicated);
					$read_theme_duplicated = fread ($open_theme_duplicated, $theme_duplicated_size);
					$search_duplicated_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_duplicated, $duplicated_name);
					$search_duplicated_version = eregi("<version>(.*)</version>", $read_theme_duplicated, $duplicated_version);
			
					if ($theme_name[1] != $duplicated_name[1]) {
						###echo "le theme choisi n'est pas le même que dans img_pack UN";
						if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
						
						$img_pack_path = opendir ($theme_path.'/img_pack/');
						while ($fichier = readdir ($img_pack_path)) {
							if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
									copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
									copy ("$theme_path/theme.xml","img_pack/theme.xml");
							}
						}
						
						$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
						while ($fichier_two = readdir ($img_pack_sub_one)) {
							if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
									copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
							}
						}
						
						$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
						while ($fichier_three = readdir ($img_pack_sub_two)) {
							if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
									copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
							}
						}
						
					}
					else if ($theme_name[1] == $duplicated_name[1]) {
						###echo "le theme choisi est le même que dans img_pack TROIS";
						if (($duplicated_version[1] != $theme_version[1]) AND ($duplicated_version[1] < $theme_version[1])) {
							
							if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
							
							$img_pack_path = opendir ($theme_path.'/img_pack/');
							while ($fichier = readdir ($img_pack_path)) {
								if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
										copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
										copy ("$theme_path/theme.xml","img_pack/theme.xml");
								}
							}
							
							$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
							while ($fichier_two = readdir ($img_pack_sub_one)) {
								if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
										copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
								}
							}
							
							$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
							while ($fichier_three = readdir ($img_pack_sub_two)) {
								if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
										copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
								}
							}
						}
					}
 				}
 		
 				else if (file_exists($theme_duplicated) AND !file_exists($theme_xml)) {
	 				###echo "fichier theme.xml n'exite pas dans le theme";
	 				if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
		 				###echo "backup existe, pas img_pack";
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						###echo "backup n'existe pas, img_pack si";
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (is_dir('img_pack_backup') AND is_dir('img_pack')){
						###echo "backup existe, img_pack aussi";
					}
					
					
					$img_pack_path = opendir ($theme_path.'/img_pack/');
					while ($fichier = readdir ($img_pack_path)) {
						if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
								copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
						}
					}
					
					$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
					while ($fichier_two = readdir ($img_pack_sub_one)) {
						if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
								copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
						}
					}
					
					$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
					while ($fichier_three = readdir ($img_pack_sub_two)) {
						if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
								copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
						}
					}
 				}
	 				
 		else if (!file_exists($theme_duplicated)){
	 		###echo "le theme.xml existe pas dans img_pack";
			if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
			
			$img_pack_path = opendir ($theme_path.'/img_pack/');
			while ($fichier = readdir ($img_pack_path)) {
				if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
						copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
						copy ("$theme_path/theme.xml","img_pack/theme.xml");
				}
			}
			
			$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
			while ($fichier_two = readdir ($img_pack_sub_one)) {
				if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
						copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
				}
			}
			
			$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
			while ($fichier_three = readdir ($img_pack_sub_two)) {
				if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
						copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
				}
			}
 		}
		 		# Fin routine
	 		}
	 			 		
	 		else if (!$search_comment_backup AND $search_define) {
		 		###echo "commentaire backup options pas trouvé mais define";
		 		fclose($open_options_file);
		 		# Definir un fichier de sauvegarde.
				$backup_file = "$options_file.backup";
				# Renommer le fichier mes_options.php afin de preserver sa virginite.
			 	rename($options_file, $backup_file);
			 	$open_backup_file = fopen($backup_file, 'r');
			 	$backup_file_size = filesize ($backup_file);
	 			$read_backup_file = fread ($open_backup_file, $backup_file_size);
		 		$new_content = "//backup_define('_DIR_IMG_PACK'";
		 		$insert_new_content = ereg_replace("define\(\'_DIR_IMG_PACK\'", $new_content, $read_backup_file);
		 		$open_options_file = fopen($options_file, 'w+');
		 		$write = fwrite($open_options_file, $insert_new_content);
				fclose($open_backup_file);
		 		fclose($open_options_file);
		 		
		 		# Debut routine.
		 		if (file_exists($theme_duplicated)) {
		 		###echo "theme.xml existe dans img_pack";
		 		$open_theme_xml = fopen($theme_xml, 'r');
				$theme_xml_size = filesize ($theme_xml);
				$read_theme_xml = fread ($open_theme_xml, $theme_xml_size);
				$search_theme_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_xml, $theme_name);
				$search_theme_version = eregi("<version>(.*)</version>", $read_theme_xml, $theme_version);
		 		
				$open_theme_duplicated = fopen($theme_duplicated, 'r');
				$theme_duplicated_size = filesize ($theme_duplicated);
				$read_theme_duplicated = fread ($open_theme_duplicated, $theme_duplicated_size);
				$search_duplicated_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_duplicated, $duplicated_name);
				$search_duplicated_version = eregi("<version>(.*)</version>", $read_theme_duplicated, $duplicated_version);
				
				if ($theme_name[1] != $duplicated_name[1]) {
					###echo "le theme choisi n'est pas le même que dans img_pack";
					if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					
					$img_pack_path = opendir ($theme_path.'/img_pack/');
					while ($fichier = readdir ($img_pack_path)) {
						if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
								copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
								copy ("$theme_path/theme.xml","img_pack/theme.xml");
						}
					}
					
					$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
					while ($fichier_two = readdir ($img_pack_sub_one)) {
						if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
								copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
						}
					}
					
					$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
					while ($fichier_three = readdir ($img_pack_sub_two)) {
						if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
								copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
						}
					}
					
				}
				else if ($theme_name[1] == $duplicated_name[1]) {
					###echo "le theme choisi est le même que dans img_pack UN";
					if (($duplicated_version[1] != $theme_version[1]) AND ($duplicated_version[1] < $theme_version[1])) {
						
						if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
						
						$img_pack_path = opendir ($theme_path.'/img_pack/');
						while ($fichier = readdir ($img_pack_path)) {
							if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
									copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
									copy ("$theme_path/theme.xml","img_pack/theme.xml");
							}
						}
						
						$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
						while ($fichier_two = readdir ($img_pack_sub_one)) {
							if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
									copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
							}
						}
						
						$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
						while ($fichier_three = readdir ($img_pack_sub_two)) {
							if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
									copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
							}
						}
					}
			}
				
	 		}
	 		
	 		else if (!file_exists($theme_duplicated)){
		 		###echo "le theme.xml existe pas dans img_pack";
				if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
				
				$img_pack_path = opendir ($theme_path.'/img_pack/');
				while ($fichier = readdir ($img_pack_path)) {
					if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
							copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
							copy ("$theme_path/theme.xml","img_pack/theme.xml");
					}
				}
				
				$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
				while ($fichier_two = readdir ($img_pack_sub_one)) {
					if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
							copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
					}
				}
				
				$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
				while ($fichier_three = readdir ($img_pack_sub_two)) {
					if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
							copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
					}
				}
	 		}
		 		# Fin routine
	 		}
 			 
 	}
 	
 	else if ($theme != "" AND $theme != "initial" AND !file_exists($options_file)) {

	 	# Routines a mettre en fonction.
 			if (file_exists($theme_duplicated)) {
	 		###echo "theme.xml existe dans img_pack";
	 		$open_theme_xml = fopen($theme_xml, 'r');
			$theme_xml_size = filesize ($theme_xml);
			$read_theme_xml = fread ($open_theme_xml, $theme_xml_size);
			$search_theme_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_xml, $theme_name);
			$search_theme_version = eregi("<version>(.*)</version>", $read_theme_xml, $theme_version);
	 		
			$open_theme_duplicated = fopen($theme_duplicated, 'r');
			$theme_duplicated_size = filesize ($theme_duplicated);
			$read_theme_duplicated = fread ($open_theme_duplicated, $theme_duplicated_size);
			$search_duplicated_name = eregi("\<prefixe>(.*)</prefixe>", $read_theme_duplicated, $duplicated_name);
			$search_duplicated_version = eregi("<version>(.*)</version>", $read_theme_duplicated, $duplicated_version);
			
			if ($theme_name[1] != $duplicated_name[1]) {
				###echo "le theme choisi n'est pas le meme que dans img_pack";
				if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
				
				$img_pack_path = opendir ($theme_path.'/img_pack/');
				while ($fichier = readdir ($img_pack_path)) {
					if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
							copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
							copy ("$theme_path/theme.xml","img_pack/theme.xml");
					}
				}
				
				$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
				while ($fichier_two = readdir ($img_pack_sub_one)) {
					if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
							copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
					}
				}
				
				$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
				while ($fichier_three = readdir ($img_pack_sub_two)) {
					if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
							copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
					}
				}
				
			}
			else if ($theme_name[1] == $duplicated_name[1]) {
				###echo "le theme choisi est le même que dans img_pack DEUX";
				if (($duplicated_version[1] != $theme_version[1]) AND ($duplicated_version[1] < $theme_version[1])) {
					
					if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					
					$img_pack_path = opendir ($theme_path.'/img_pack/');
					while ($fichier = readdir ($img_pack_path)) {
						if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
								copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
								copy ("$theme_path/theme.xml","img_pack/theme.xml");
						}
					}
					
					$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
					while ($fichier_two = readdir ($img_pack_sub_one)) {
						if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
								copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
						}
					}
					
					$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
					while ($fichier_three = readdir ($img_pack_sub_two)) {
						if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
								copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
						}
					}
				}
		}
			
 		}
 		
 		else if (!file_exists($theme_duplicated)){
	 		###echo "le theme.xml existe pas dans img_pack";
			if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
					else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
						rename ("img_pack", "img_pack_backup");
						mkdir ("img_pack", 0700);
						mkdir ("img_pack/icones_barre", 0700);
						mkdir ("img_pack/icones", 0700);
					}
			
			$img_pack_path = opendir ($theme_path.'/img_pack/');
			while ($fichier = readdir ($img_pack_path)) {
				if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
						copy ("$theme_path/img_pack/$fichier","img_pack/$fichier");
						copy ("$theme_path/theme.xml","img_pack/theme.xml");
				}
			}
			
			$img_pack_sub_one = opendir ($theme_path.'/img_pack/icones_barre/');
			while ($fichier_two = readdir ($img_pack_sub_one)) {
				if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
						copy ("$theme_path/img_pack/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
				}
			}
			
			$img_pack_sub_two = opendir ($theme_path.'/img_pack/icones/');
			while ($fichier_three = readdir ($img_pack_sub_two)) {
				if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
						copy ("$theme_path/img_pack/icones/$fichier_three","img_pack/icones/$fichier_three");
				}
			}
 		}	 
 	}

 	else if ($theme == "initial") {
	 	###echo "veut revenir initial";
	 	if (is_dir('img_pack_backup') AND !is_dir('img_pack')) {
			mkdir ("img_pack", 0700);
			mkdir ("img_pack/icones_barre", 0700);
			mkdir ("img_pack/icones", 0700);
			}
		else if (!is_dir('img_pack_backup') AND is_dir('img_pack')){
			rename ("img_pack", "img_pack_backup");
			mkdir ("img_pack", 0700);
			mkdir ("img_pack/icones_barre", 0700);
			mkdir ("img_pack/icones", 0700);
		}
			
			$img_pack_backup_one = opendir ('img_pack_backup/');
			while ($fichier = readdir ($img_pack_backup_one)) {
				if ($fichier != "." && $fichier != ".." && $fichier != ".svn" && $fichier != "icones_barre" && $fichier != "icones") {
						copy ("img_pack_backup/$fichier","img_pack/$fichier");
				}
			}
			
			$img_pack_backup_two = opendir ('img_pack_backup/icones_barre/');
			while ($fichier_two = readdir ($img_pack_backup_two)) {
				if ($fichier_two != "." && $fichier_two != ".." && $fichier_two != ".svn") {
						copy ("img_pack_backup/icones_barre/$fichier_two","img_pack/icones_barre/$fichier_two");
				}
			}
			
			$img_pack_backup_three = opendir ('img_pack_backup/icones/');
			while ($fichier_three = readdir ($img_pack_backup_three)) {
				if ($fichier_three != "." && $fichier_three != ".." && $fichier_three != ".svn") {
						copy ("img_pack_backup//icones/$fichier_three","img_pack/icones/$fichier_three");
				}
		}
 	}
 	
 	echo "<a name='access-c' href='#access-c' accesskey='c'></a><div class='cadre-r'><div style='position: relative;'><div class='cadre-titre' style='margin: 0px;'>";
 	echo '<INPUT type=radio name="theme" value="initial"';
 		if ($_REQUEST['theme'] == "initial") {
	 		echo "checked";
 		}
 	echo ">";
 	echo "<strong>Revenir &agrave; l'habillage d'origine</strong>";
 	echo '</div></div><div class="cadre-padding" style="overflow:hidden;">';
	    		echo "</div></div><div style='height: 5px;'></div>";
 	
 	$dossier = opendir (_DIR_PLUGIN_HABILLAGES.'/prive/themes/');
	while ($fichier = readdir ($dossier)) {
    	if ($fichier != "." && $fichier != "..") {
	    	echo "<a name='access-c' href='#access-c' accesskey='c'></a><div class='cadre-r'><div style='position: relative;'><div class='cadre-titre' style='margin: 0px;'>";
	    	echo '<INPUT type=radio name="theme" value="'.$fichier.'"';
	    	if ($_REQUEST['theme'] == "" AND file_exists($theme_duplicated)) {
		    	if (fopen($theme_duplicated, 'r') == TRUE) {
		    	###echo "Pas de theme choisi et le fichier theme est reconnu dans img_pack";
		    	$open_theme_duplicated_file = fopen($theme_duplicated, 'r');
				$theme_duplicated_file_size = filesize ($theme_duplicated);
				$read_theme_duplicated = fread ($open_theme_duplicated_file, $theme_duplicated_file_size);
				# Mettre dans l'expression reguliere ci-dessous la possibilite de reconnaitre le prefixe
				# meme si le prefixe n'est pas colle aux balises <prefixes>.
				$search_duplicated_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_duplicated, $duplicated_name);
				echo $duplicated_name[1];
				if ($duplicated_name[1] == $fichier) {
		    	echo " checked";
	    		}
	    		fclose($open_theme_duplicated_file);
    			}
	    	}
	    	else if ($_REQUEST['theme'] == $fichier) {
		    	echo " checked";
	    	}
	    	echo ">";
	    	
	    	$theme_file = $plugin_directory.'/prive/themes/'.$fichier.'/theme.xml';
	    	if (file_exists($theme_file)) {
	        	$open_theme_file = fopen($theme_file, 'r');
				$theme_file_size = filesize ($theme_file);
				$read_theme_file = fread ($open_theme_file, $theme_file_size);
				$search_theme_name = eregi("<nom>(.*)</nom>", $read_theme_file, $theme_name);
				$search_theme_author = eregi("<auteur>(.*)</auteur>", $read_theme_file, $theme_author);
				$search_theme_version = eregi("<version>(.*)</version>", $read_theme_file, $theme_version);
				$search_theme_description = eregi("<description>(.*)</description>", $read_theme_file, $theme_description);
				echo '<strong>'.$theme_name[1].'</strong> version '.$theme_version[1].'</div></div><div class="cadre-padding" style="overflow:hidden;">';
				echo '<i><medium>Auteur : '.$theme_author[1].'</medium></i><br />';
				echo '<small>'.$theme_description[1].'</small>';
	        	echo "</div></div><div style='height: 5px;'></div>";
	        	fclose($open_theme_file);
    		}
    		
    		else {
	    		echo '<strong>'.$fichier.'</strong>';
	    		echo '</div></div><div class="cadre-padding" style="overflow:hidden;">';
	    		echo "</div></div><div style='height: 5px;'></div>";
    		}
    	}
	}
	closedir ($dossier);
	
	echo '<input type="submit" value="'._T('valider').'"/>';
 	fin_cadre_trait_couleur();
#### FIN DE L'ENCADRE QUI GERE L'HABILLAGE PRIVE ########################################

echo "<br />";

#### DEBUT DE L'ENCADRE QUI GERE L'HABILLAGE PUBLIC #####################################
debut_cadre_trait_couleur("../"._DIR_PLUGIN_HABILLAGES."/img_pack/habillage_public-32.png", false, "", _T('habillageprive:titre_habillage_public'));
 	
	$squelette = $_REQUEST['squelette'];
 	$plugin_options_file = "$plugin_directory/habillages_options.php";
 	
 	if ($squelette == "initial") {
		$open_plugin_options_file = fopen($plugin_options_file, 'w+');
		$empty_content = "<?php\n?>";
		$write = fwrite($open_plugin_options_file, $empty_content);
		fclose($open_plugin_options_file);
	}
	
	else if ($squelette != "") {
		$cleaned_path = str_replace('../', "", _DIR_PLUGIN_HABILLAGES);
	 	$open_plugin_options_file = fopen($plugin_options_file, 'w+');
		$new_content = "<?php\n\$GLOBALS['dossier_squelettes']='".$cleaned_path."/public/themes/$squelette/squelettes';\n?>"; 
		$write = fwrite($open_plugin_options_file, $new_content);
		fclose($open_plugin_options_file);
	}

	echo "<a name='access-c' href='#access-c' accesskey='c'></a><div class='cadre-r'><div style='position: relative;'><div class='cadre-titre' style='margin: 0px;'>";
 	echo '<INPUT type=radio name="squelette" value="initial"';
 		if ($_REQUEST['squelette'] == "initial") {
	 		echo "checked";
 		}
 	echo ">";
 	echo "<strong>Revenir &agrave; l'habillage d'origine</strong>";
 	echo '</div></div><div class="cadre-padding" style="overflow:hidden;">';
	echo "</div></div><div style='height: 5px;'></div>";
 	
 	$dossier = opendir ($plugin_directory.'/public/themes/');
	while ($fichier = readdir ($dossier)) {
    	if ($fichier != "." && $fichier != "..") {
	    	echo "<a name='access-c' href='#access-c' accesskey='c'></a><div class='cadre-r'><div style='position: relative;'><div class='cadre-titre' style='margin: 0px;'>";
	    	echo '<INPUT type=radio name="squelette" value="'.$fichier.'"';
	    	if ($_REQUEST['squelette'] == "") {
		    	$cleaned_path = str_replace('../', "", _DIR_PLUGIN_HABILLAGES);
		    	$plugin_options_file = "$plugin_directory/habillages_options.php";
		    	$open_plugin_options_file = fopen($plugin_options_file, 'r');
				$plugin_options_file_size = filesize ($plugin_options_file);
				$read_plugin_options_file = fread ($open_plugin_options_file, $plugin_options_file_size);
				$search_skel_name = eregi("\/public\/themes\/(.*)\/squelettes", $read_plugin_options_file, $skel_name);
				if ($skel_name[1] == $fichier) {
		    	echo " checked";
	    		}
	    		fclose($open_plugin_options_file);
	    	}
	    	else if ($_REQUEST['squelette'] == $fichier) {
		    	echo " checked";
	    	}
	    	echo ">";
        	
        	$theme_file = $plugin_directory.'/public/themes/'.$fichier.'/theme.xml';
	    	if (file_exists($theme_file)) {
        	$open_theme_file = fopen($theme_file, 'r');
			$theme_file_size = filesize ($theme_file);
			$read_theme_file = fread ($open_theme_file, $theme_file_size);
			$search_theme_name = eregi("<nom>(.*)</nom>", $read_theme_file, $theme_name);
			$search_theme_name = eregi("<auteur>(.*)</auteur>", $read_theme_file, $theme_author);
			$search_theme_name = eregi("<version>(.*)</version>", $read_theme_file, $theme_version);
			$search_theme_name = eregi("<description>(.*)</description>", $read_theme_file, $theme_description);
			echo '<strong>'.$theme_name[1].'</strong> version '.$theme_version[1].'</div></div><div class="cadre-padding" style="overflow:hidden;">';
				echo '<i><medium>Auteur : '.$theme_author[1].'</medium></i><br />';
				echo '<small>'.$theme_description[1].'</small>';
	        	echo "</div></div><div style='height: 5px;'></div>";
        	fclose($open_theme_file);
    		}
    		
    		else {
	    		echo '<strong>'.$fichier.'</strong>';
	    		echo '</div></div><div class="cadre-padding" style="overflow:hidden;">';
	    		echo "</div></div><div style='height: 5px;'></div>";
    		}
    	}
	}
	closedir ($dossier);
	
	echo '<input type="submit" value="'._T('valider').'"/>';
	
	echo '</form>';
fin_cadre_trait_couleur();
#### FIN DE L'ENCADRE QUI GERE L'HABILLAGE PUBLIC #######################################
	
  } 
  
  fin_page();
  
}

?>