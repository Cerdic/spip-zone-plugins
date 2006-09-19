<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */
include_spip('inc/charts');

function charts_duplique_chart(){
	$duplique = intval(_request('duplique_chart'));
	if ($duplique && charts_chart_administrable($duplique)){
		// creation
		$structure = array();
		spip_query("INSERT INTO spip_charts (structure) VALUES ('".
			addslashes(serialize($structure))."')");
		$id_chart = spip_insert_id();
		$query = "SELECT * FROM spip_charts WHERE id_chart=$duplique";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			spip_abstract_insert("spip_charts","(titre,descriptif,largeur,hauteur,code,background)",
			"(".
				spip_abstract_quote(_T('charts:copie_de',array('titre'=>$row['titre']))).", ".
				spip_abstract_quote($row['descriptif']).", ".
				spip_abstract_quote($row['largeur']).", ".
				spip_abstract_quote($row['hauteur']).", ".
				spip_abstract_quote($row['code']).", ".
				spip_abstract_quote($row['background']) . ")");
		}
	}	
}

function exec_charts_tous(){
  include_spip("inc/presentation");

  charts_verifier_base();
	charts_duplique_chart();
	
	debut_page(_T("charts:tous_graphiques"), "documents", "charts");
	debut_gauche();
	debut_boite_info();
	echo _T("charts:tous_graphiques_desc");
	fin_boite_info();
	
	debut_droite();
	
	charts_afficher_charts(_T("charts:tous_graphiques"),
		array(
		"SELECT"=>"charts.*",
		"FROM" => "spip_charts AS charts",
		"JOIN" => "",
		"WHERE" => "",
		"GROUP BY" => "charts.id_chart",
		"ORDER BY" => "titre"));
	
	echo "<br />\n";
	
	if (charts_chart_editable()) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('charts_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_T("charts:icone_creer_chart"), $link, "../"._DIR_PLUGIN_CHARTS. "/img_pack/chart-24.gif", "creer.gif");
		echo "</div>";
	}
	
	
	
	fin_page();
}

?>
