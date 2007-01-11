<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');   // for spip presentation functions
include_spip('inc/layer');          // for spip layer functions
include_spip('inc/utils');          // for _request function
include_spip('inc/plugin');         // xml function


//page de configuration
function exec_archive_configuration() {
	
	
	$archive_action = _request('archive_action');
	$archive_submit = _request('submit');
	
	switch($archive_action) {
		case "install":
			include_spip('inc/archive_install');
			echo archive_install();
			break;
		case "uninstall":
			include_spip('inc/archive_install');
			echo archive_uninstall();		
			break;
	}
	

	
	debut_page("configuration archive",'configuration','archive_configuration');
	echo "<br />";
  	gros_titre("Configuration des archives");
  	
  	
  	debut_gauche();
  		debut_boite_info();
	  	//debut_cadre("info");
	  		echo "Explication";
	  		echo "</p>Cette page sert à lancer l'installation spécifique au plugin</p>";
	  		echo "<ul>";
	  			echo "<li>le bouton \""._T('archive:install')."\" configure la base spip</li";
	  			echo "<li>le bouton \""._T('archive:uninstall')."\" supprime les modifications apportées à la base spip</li>";
	  		echo "</ul>";
	  		echo "</p>Cette page comportera peut etre des options complémentaires telles que des historisation sur les archives ou autre(s) besoins(s) exprimé(s) dans le futur</p>";
	  	//fin_cadre("info");
	  	fin_boite_info();
	fin_gauche();

  	debut_droite();
	  	debut_cadre_trait_couleur('',false,'','Installation');
	  	
	  		debut_boite_info();
	  			echo "<b>";
	  			if 	(isset($GLOBALS['meta']['archive_version'])) {
					echo "Le plugin est correctement installé et configuré";  
				} 
				else {
					echo "Veuillez configurer le plugin";
				}
				echo "</b>";
	  		fin_boite_info();
	  		echo '<br/>';
	  	
	  		debut_cadre_formulaire();
  			echo '<form action="'.generer_url_ecrire("archive_configuration", "archive_action=install").'" method="post">';
  				echo 'installer archive';
				echo '<div style="text-align:right"><input type="submit" value="'._T('archive:install').'"></div>';
  			echo '</form>';
  			fin_cadre_formulaire();
  			echo "<br/>";
  			debut_cadre_formulaire();
  			echo '<form action="'.generer_url_ecrire("archive_configuration", "archive_action=uninstall").'" method="post">';
  				echo 'desinstaller archive';
				echo '<div style="text-align:right"><input type="submit" value="'._T('archive:uninstall').'"></div>';
  			echo '</form>'; 
  			fin_cadre_formulaire();
		fin_cadre_trait_couleur();
}
?>
