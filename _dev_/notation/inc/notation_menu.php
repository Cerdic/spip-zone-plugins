<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr) / b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage des menus
*  
**/

include_spip('inc/presentation');

function ecrire_menu($menu='table'){
	// Verifier l'acces
	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee;

	debut_page(_T('notation:notation'), "naviguer", "notation");

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)){
		// Pas d'acces
		debut_gauche();
		debut_droite();
		gros_titre("Plugin "._T('notation:notation'));
		return true;
	}

	// Informations
	debut_gauche();
	echo "<br/><br/>";
	global $version;
	debut_cadre_couleur ('',false,'',"Plugin "._T('notation:notation'));
	echo ("<p>Documentation sur spip-contrib :</p>");
	echo ("<p><a href='http://www.spip-contrib.net/Notation-d-articles-SPIP'>http://www.spip-contrib.net/Notation-d-articles-SPIP</a></p>");
	echo ("<small>v.0.6 &copy; 2008</small>");
	fin_cadre_couleur();

	debut_droite();

	// Afficher les onglets
	gros_titre("Plugin "._T('notation:notation'));
	echo debut_onglet().
	onglet(_T('notation:afficher_tables'), generer_url_ecrire("notation"), 'table', $menu, '../'._DIR_PLUGIN_NOTATION.'img_pack/notation.png').
	onglet(_T('notation:param'), generer_url_ecrire("notation_param"), 'param', $menu, '../'._DIR_PLUGIN_NOTATION.'img_pack/spip_mecano_24.png').
	fin_onglet();


	return true;
}
   
?>