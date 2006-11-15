<?php

//    Fichier créé pour SPIP.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Quentin Drouet
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_SPIPMOTION',(_DIR_PLUGINS.end($p)));

function exec_config_spipmotion() {
	global $connect_statut, $connect_toutes_rubriques,$connect_id_auteur;

	include_spip("inc/presentation");

	debut_page('&laquo; '._T('spipmotion:titre_page').' &raquo;', 'configurations', 'spipmotion');

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		exit;
	}

	if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {

//Partie de code du plugin saveauto de cyaltern
         $page_save_conf = _DIR_PLUGIN_SPIPMOTION."/inc/spipmotion_conf.php";

		// traitement des donnees postees dans le formulaire de config : recreer le fichier spipmotion_conf.php
		if (isset($_POST['valide_config'])) {
			$T_params = array('chemin', 'width', 'height', 'bitrate', 'fps', 'frequence_audio', 'bitrate_audio');
			$a_ecrire = "<?php ";

			foreach ($T_params as $p) {
				$a_ecrire .= "\n $".$p." = ";
				if (($_POST[$p] != "true" AND $_POST[$p] != "false" AND ereg("[a-zA-Z_.!@#$&*:+=]", $_POST[$p]) != false) OR $_POST[$p] == "") $a_ecrire .= '"';
					$a_ecrire .= $_POST[$p];
				if (($_POST[$p] != "true" AND $_POST[$p] != "false" AND ereg("[a-zA-Z_.!@#$&*:+=]", $_POST[$p]) != false) OR $_POST[$p] == "") $a_ecrire .= '"';
					$a_ecrire .= ";";
			}
			$a_ecrire .= "\n ?>";
			if ($fconf = @fopen($page_save_conf, "wt")) {
    				fwrite($fconf, $a_ecrire);
    				fclose($fconf);
			}
			else {
				 echo '<font color=red><strong>'._T('spipmotion:erreur_ecrire_conf').'</strong></font>';
			}
		}
		include($page_save_conf);

	echo '<br /><br />';
	
	gros_titre(_T('spipmotion:gros_titre'));
	
	barre_onglets("configuration", "config_types_documents");
	
	/*Affichage*/
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('typesdocuments:help'));
	fin_boite_info();
	
	debut_droite();
	
	include_spip('inc/config');
	avertissement_config();

	echo "\r\n<form action=\"$PHP_SELF?exec=config_spipmotion\" name=\"frm_config\" method=\"post\">";
	debut_cadre_trait_couleur("plugin-24.gif", false, "", _T('spipmotion:options_config'));

	debut_cadre_couleur();
		echo "<strong>"._T('spipmotion:chemin_executable')."</strong><br />";
		echo "<input type='text' name='chemin' id='chemin' value='".$chemin."'>";
	fin_cadre_couleur();
	
	debut_cadre_couleur();
		echo "<strong>"._T('spipmotion:width_video')."</strong>";
		echo "<input type='text' name='width' id='width' value='".$width."' style='width:30px;' /><br />";
		echo "<strong>"._T('spipmotion:height_video')."</strong>";
		echo "<input type='text' name='height' id='height' value='".$height."' style='width:30px;' /><br />";
		echo "<strong>"._T('spipmotion:bitrate')."</strong>";
		echo "<input type='text' name='bitrate' id='bitrate' value='".$bitrate."' style='width:30px;' /><br />";
		echo "<strong>"._T('spipmotion:framerate')."</strong>";
		echo "<input type='text' name='fps' id='fps' value='".$fps."' style='width:20px;' />";
	fin_cadre_couleur();

	debut_cadre_couleur();
		echo "<strong>"._T('spipmotion:frequence_audio')."</strong>";
		echo "<input type='text' name='frequence_audio' id='frequence_audio' value='".$frequence_audio."' style='width:50px;' /><br />";
		echo "<strong>"._T('spipmotion:bitrate_audio')."</strong>";
		echo "<input type='text' name='bitrate_audio' id='bitrate_audio' value='".$bitrate_audio."' style='width:20px;' />";
         fin_cadre_couleur();

		echo "<input type='submit' name='valide_config' id='valide_config' value='"._T('spipmotion:valider')."' style='float: right;'><br />";
	fin_cadre_trait_couleur();
        echo "</form>";
  }
  fin_page();
}
?>
