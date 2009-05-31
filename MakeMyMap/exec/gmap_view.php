<?php
/*
 * Googlemap API
 * 
 *
 * 
 */

include_spip("inc/utils");
include_spip("inc/presentation");

function exec_gmap_view(){
	global $connect_statut,$spip_lang_right;
	$commencer_page = charger_fonction('commencer_page', 'inc') ; 
	echo $commencer_page(_T('mymap:voir'), "", "") ;
	
	echo debut_gauche('', true);
	
	echo bloc_des_raccourcis(icone_horizontale(_T('Configurer Google Map'), generer_url_ecrire('mymap_config'), _DIR_PLUGIN_MYMAP."img_pack/correxir.png","", false));
	
	
	echo debut_droite('', true);
	// Configuration du systeme geographique
	echo debut_grand_cadre(true);
	if (autoriser('administrer','mymap')) {
		
		echo debut_cadre('r',_DIR_PLUGIN_MYMAP."img_pack/correxir.png");
		
		echo afficher_objets('article',_T('Liste des Articles ayant un plan Google Map'),array("FROM" =>"spip_articles AS articles, spip_mymap_articles AS lien ", "WHERE" => "articles.id_article=lien.id_article", 'ORDER BY' => "articles.date DESC"));
		
		//echo afficher_articles(_T('Liste des Articles ayant un plan Google Map'),	array("FROM" =>"spip_articles AS articles, spip_mymap_articles AS lien ", "WHERE" => "articles.id_article=lien.id_article", 'ORDER BY' => "articles.date DESC"));
		echo fin_cadre('r');
	}
	echo fin_grand_cadre(true);
	echo fin_gauche();
	echo fin_page();
}


?>