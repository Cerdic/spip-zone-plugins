<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_admin_mnogo(){
	global $connect_statut,$connect_toutes_rubriques;

	include_spip('inc/mnogo_distant');
	mnogo_verifier_base();
	
	include_spip("inc/presentation");
		
	debut_page(_L("Interface mnoGoSearch"), "mnoGoSearch", "mnoGoSearch");

	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Cette page permet de configurer l\'interrogation du moteur de recherche mnoGoSearch<br/> Configurez votre mnoGoSearch avec les exemples fournis dans le repertoire mnogosearch du plugin.'));
	fin_boite_info();


	debut_droite();
	gros_titre(_L('Moteur de Recherche mnoGoSearch'));
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	// parametres generaux : id-syndication et url web service
	echo generer_url_post_ecrire('admin_mnogo');
	echo "<div>";
	
	$mnogo_url_search = _request('mnogo_url_search');
	if ($mnogo_url_search!==NULL){
		if (substr($mnogo_url_search,-1)=='/')
			$mnogo_url_search = substr($mnogo_url_search,0,strlen($mnogo_url_search)-1);
		ecrire_meta('mnogo_url_search',$mnogo_url_search);
		ecrire_metas();
	}
	if (_request('vider_cache')!=NULL){
		spip_query("DELETE FROM spip_mnogosearch");
		spip_query("DELETE FROM spip_mnogosearch_summary");
	}

	$mnogo_url_search = isset($GLOBALS['meta']['mnogo_url_search'])?$GLOBALS['meta']['mnogo_url_search']:"";
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	echo "<label for='mnogo_url_search'><strong>"._L('Url d\'interrogation du moteur mnoGoSearch')."</strong></label><br/>";
	echo "<input type='text' label='mnogo_url_search' name='mnogo_url_search' value=\"".entites_html($mnogo_url_search)."\" class='formo' />";
	echo "</div>";

	echo "<p style='text-align:right;'>";
	echo "<input type='submit' name='submit' value='"._T('Modifier')."' class='fondo' />";
	echo "</p></div></form>";

	echo generer_url_post_ecrire('admin_mnogo');
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	$res = spip_query("SELECT COUNT(hash) AS n FROM spip_mnogosearch");
	$nb_results = spip_fetch_array($res);
	$nb_results = reset($nb_results);
	$res = spip_query("SELECT COUNT(hash) AS n FROM spip_mnogosearch_summary");
	$nb_recherches = spip_fetch_array($res);
	$nb_recherches = reset($nb_recherches);
	echo "La base contient en cache $nb_results r&eacute;sultats correspondants &agrave $nb_recherches recherches diff&eacute;rentes";
	echo "<p style='text-align:right;'>";
	echo "<input type='submit' name='vider_cache' value='"._T('bouton_vider_cache')."' class='fondo' />";
	echo "</p></div></form>";

	fin_page();
}


?>