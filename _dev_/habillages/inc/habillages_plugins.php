<?php

function habillages_affichage_plugins($fichier_plugin_xml) {
	global $couleur_claire;
	global $couleur_foncee;
	
	lire_fichier($fichier_plugin_xml, $texte_plugin_xml);
	$arbre = parse_plugin_xml($texte_plugin_xml);
	$arbre = $arbre['plugin'][0];
	
	# Contenu du fichier plugin.xml dans le dossier choisi.
	$nom_plugin = applatit_arbre($arbre['nom']);
	$auteur_plugin = applatit_arbre($arbre['auteur']);
	$etat_plugin = applatit_arbre($arbre['etat']);
	$version_plugin = applatit_arbre($arbre['version']);
	$description_plugin = applatit_arbre($arbre['description']);
	$fonctions_plugin = trim(applatit_arbre($arbre['fonctions']));
	$options_plugin = trim(applatit_arbre($arbre['options']));
	$prefix_plugin = trim(applatit_arbre($arbre['prefix']));
	$pipeline_plugin = trim(applatit_arbre($arbre['pipeline']));
	
	debut_boite_info();
	echo "<div style='background-color:$couleur_claire'>";
	echo "<input type='checkbox' name='statusplug_$dossier_plugin'$checked>";
	echo "<strong>".$nom_plugin."</strong>(version ".$version_plugin.")<label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label><br /><br /></div>";
	# Laisser la possibilite de definir le nom et le chemin de la capure ecran
	# dans theme.xml.
	echo '<div style="float:right";><img src="'.dirname($fichier_plugin_xml).'/capture.png" alt="description" class="preview" /></div>';
	echo "<small>".propre($description_plugin)."</small><br /><br /><hr>";
	echo "<div class='auteur'>".propre($auteur_plugin)."</div><hr>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/".$etat.".png' />";
	echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
	fin_boite_info();
}
?>