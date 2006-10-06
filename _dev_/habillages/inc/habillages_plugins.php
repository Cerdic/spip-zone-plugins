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
	$chemin_plugin_complet = dirname($fichier_plugin_xml);
	$chemin_plugin_court = substr($chemin_plugin_complet, strlen(_DIR_PLUGINS));
	
	if (isset($etat_plugin))
		$etat = trim($etat_plugin);
		switch ($etat) {
			case 'experimental':
				$couleur_txt = "CA2F2F";
				$titre_etat = _T('habillages:plugin_etat_experimental');
				break;
			case 'test':
				$couleur_txt = "E85600";
				$titre_etat = _T('habillages:plugin_etat_test');
				break;
			case 'stable':
				$couleur_txt = "149E06";
				$titre_etat = _T('habillages:plugin_etat_stable');
				break;
			default:
				$couleur_txt = "900B06";
				$titre_etat = _T('habillages:plugin_etat_developpement');
				break;
		}
	
	$lire_meta_plugin = isset($GLOBALS['meta']['plugin'])?$GLOBALS['meta']['plugin']:'';
	$plugin_actif = ereg($chemin_plugin_court, $lire_meta_plugin, $pleug_actif);
	
	if ($pleug_actif != "") {
		$checked = " checked='checked'";
	}
	else {
		$checked = "";
	}
		
	debut_boite_info();
	echo "<div style='background-color:$couleur_claire'>";
	echo "<input type='radio' name='statusplug' value='$chemin_plugin_court'$checked>";
	echo "<strong>".$nom_plugin."</strong>(version ".$version_plugin.")<label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label><br /><br /></div>";
	# Laisser la possibilite de definir le nom et le chemin de la capure ecran
	# dans theme.xml.
	echo '<div style="float:right";><img src="'.$chemin_plugin_complet.'/capture.png" alt="description" class="preview" /></div>';
	echo "<small>".propre($description_plugin)."</small><br /><br /><hr>";
	echo "<div class='auteur'>".propre($auteur_plugin)."</div><hr>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/".$etat.".png' />";
	echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
	fin_boite_info();
}
?>