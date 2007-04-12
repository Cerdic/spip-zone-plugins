<?php

function habillages_affichage_squelettes($fichier_plugin_xml) {
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
	
		if (_request('exec')=='habillages_squelettes'){
			lire_metas();
			$lire_meta_habillages = array($GLOBALS['meta']['habillages_squelettes']);
		}

	if ($lire_meta_habillages[0] == $chemin_plugin_court) {
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
	echo '<div style="float:right";><img src="'.$chemin_plugin_complet.'/captureBW.png" alt="" class="preview" /></div>';
	# Ajouter : si theme.xml ne contient pas de theme.xml, on prend la description de plugin.xml. 
	# Il est necessaire que theme.xml puisse definir les caracteristiques d'un squelette, d'un style, 
	# d'un jeu d'images.
	echo "<small>".propre($description_plugin)."</small><br /><br /><hr>";
	echo "<div class='auteur'>".propre($auteur_plugin)."</div><hr>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/".$etat.".png' />";
	echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
	fin_boite_info();
}

function habillages_affichage_themes($fichier_plugin_xml) {
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
	
		if (_request('exec')=='habillages_themes'){
			lire_metas();
			$lire_meta_habillages = array($GLOBALS['meta']['habillages_squelettes']);
		}

	if ($lire_meta_habillages[0] == $chemin_plugin_court) {
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
	echo '<div style="float:right";><img src="'.$chemin_plugin_complet.'/captureBW.png" alt="description" class="preview" /></div>';
	# Ajouter : si theme.xml ne contient pas de theme.xml, on prend la description de plugin.xml. 
	# Il est necessaire que theme.xml puisse definir les caracteristiques d'un squelette, d'un style, 
	# d'un jeu d'images.
	echo "<small>".propre($description_plugin)."</small><br /><br /><hr>";
	echo "<div class='auteur'>".propre($auteur_plugin)."</div><hr>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/".$etat.".png' />";
	echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
	fin_boite_info();
}

?>