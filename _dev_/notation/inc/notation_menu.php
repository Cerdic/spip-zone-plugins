<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage des menus
*  
**/

include_spip('inc/presentation');

function ecrire_menu($menu='table')
{ // Verifier l'acces
 	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee;

 	debut_page(_T('notation:notation'), "naviguer", "notation");

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) 
	{ // Pas d'acces
    debut_gauche();
		debut_droite();
	  gros_titre("<center>Plugin "._T('notation:notation')."</center>");
		return true;
	}

  // Informations
	debut_gauche();
	echo "<br/><br/>";
	global $version;
	debut_cadre_couleur ('',false,'',"Plugin "._T('notation:notation')." <small style='color:white'>par jmv</small>");
		echo ("<small style='color:white'>v.0.2 &copy; 2007</small>");
	fin_cadre_couleur();

	debut_droite();
	
	// Afficher les onglets
	gros_titre("<center>Plugin "._T('notation:notation')."</center>");
	echo debut_onglet().
	onglet(_T('notation:afficher_tables'), generer_url_ecrire("notation"), 'table', $menu, '../'._DIR_PLUGIN_NOTATION.'/img_pack/notation.png').
	onglet(_T('notation:param'), generer_url_ecrire("notation_param"), 'param', $menu, '../'._DIR_PLUGIN_NOTATION.'/img_pack/spip_mecano_24.png').
	onglet(_T('notation:aide'), generer_url_ecrire("notation_help"), 'help', $menu, '../'._DIR_PLUGIN_NOTATION.'/img_pack/notation_help.png').
	fin_onglet();


	return true;

}
   
?>