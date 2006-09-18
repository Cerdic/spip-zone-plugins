<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

// Fonction qui gere les habillages. Tentative de faire la fonction sans appel a la
// base de donnees, et donc sans manipulation sql.
function exec_config_habillages() {
  global $connect_statut, $connect_toutes_rubriques;

  include_spip("inc/presentation");
  include_spip ("base/abstract_sql");
  include_spip("inc/espace_prive");
  include_spip("inc/plugin");


  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
  debut_page('&laquo; '._T('habillageprive:titre_page').' &raquo;', 'configurations', 'habillage_prive','',_DIR_PLUGIN_HABILLAGES.'/img_pack/habillage_prive.css');
	echo _T('avis_non_acces_page');
	exit;
  }
  
  // mettre a jour le theme prive pour en profiter tout de suite
  if (($c=_request('theme'))!==NULL){
  	include_spip('inc/meta');
  	ecrire_meta('habillage_prive',$c);
  	ecrire_metas();
  }

  debut_page('&laquo; '._T('habillageprive:titre_page').' &raquo;', 'configurations', 'habillage_prive','',_DIR_PLUGIN_HABILLAGES.'/img_pack/habillage_prive.css');
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
 	 	
 	echo "<a name='access-c' href='#access-c' accesskey='c'></a><div class='cadre-r'><div style='position: relative;'><div class='cadre-titre' style='margin: 0px;'>";
 	echo '<INPUT type=radio name="theme" value=""';
 	if ($GLOBALS['meta']['habillage_prive'] == "")
	 		echo "checked='checked'";
 	echo ">";
 	echo "<strong>Revenir &agrave; l'habillage d'origine</strong>";
 	echo '</div></div><div class="cadre-padding" style="overflow:hidden;">';
	echo "</div></div><div style='height: 5px;'></div>";

	$liste_themes = preg_files(_DIR_PLUGIN_HABILLAGES.'/prive/themes/',"/theme[.]xml$");
	foreach ($liste_themes as $fichier){
			$c = dirname($fichier)."/img_pack/";
    	echo "<a name='access-c' href='#access-c' accesskey='c'></a><div class='cadre-r'><div style='position: relative;'><div class='cadre-titre' style='margin: 0px;'>";
    	echo '<INPUT type=radio name="theme" value="'.$c.'"';
		 	if ($GLOBALS['meta']['habillage_prive'] == $c)
			 		echo "checked='checked'";
    	echo ">";
    	
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			
			$theme_name = applatit_arbre($arbre['nom']);
			$theme_author = applatit_arbre($arbre['auteur']);
			$theme_version = applatit_arbre($arbre['version']);
			$theme_description = applatit_arbre($arbre['description']);
			echo '<strong>'.$theme_name.'</strong> version '.$theme_version.'</div></div><div class="cadre-padding" style="overflow:hidden;">';
			echo '<i><medium>Auteur : '.$theme_author.'</medium></i><br />';
			echo '<small>'.$theme_description.'</small>';
    	echo "</div></div><div style='height: 5px;'></div>";
	}
	
	echo '<input type="submit" value="'._T('valider').'"/>';
 	fin_cadre_trait_couleur();
#### FIN DE L'ENCADRE QUI GERE L'HABILLAGE PRIVE ########################################

echo "<br />";

#### DEBUT DE L'ENCADRE QUI GERE L'HABILLAGE PUBLIC #####################################
debut_cadre_trait_couleur("../"._DIR_PLUGIN_HABILLAGES."/img_pack/habillage_public-32.png", false, "", _T('habillageprive:titre_habillage_public'));
 	
	$squelette = $_REQUEST['squelette'];
 	$plugin_options_file = "$plugin_directory/habillages_options.php";
 	$plugin_directory = _DIR_PLUGIN_HABILLAGES;
 	
 	if ($squelette == "initial") {
	 	chmod($plugin_options_file, 0777);
		$open_plugin_options_file = fopen($plugin_options_file, 'w+');
		$empty_content = "<?php\n?>";
		$write = fwrite($open_plugin_options_file, $empty_content);
		fclose($open_plugin_options_file);
	}
	
	else if ($squelette != "") {
		$cleaned_path = str_replace('../', "", _DIR_PLUGIN_HABILLAGES);
		chmod($plugin_options_file, 0777);
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
				echo '<div style="float:left";><img src="../'.$plugin_directory.'/public/themes/'.$fichier.'/capture.png" alt="description" class="preview" /></div>';

				echo '<small>'.$theme_description[1].'</small>';
	        	echo "</div></div><div style='height: 5px;'></div>";
        	fclose($open_theme_file);
    		}
    		
    		else {
	    		echo '<strong>'.$fichier.'</strong>';
	    		echo '</div></div><div class="cadre-padding" style="overflow:hidden;">';
	    		echo '<div style="float:left";><img src="../'.$plugin_directory.'/public/themes/'.$fichier.'/capture.png" alt="description" class="preview" /></div>';
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