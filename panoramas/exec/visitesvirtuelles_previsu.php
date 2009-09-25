<?php

include_spip('inc/panoramas_edit');
	

function exec_visitesvirtuelles_previsu(){
	
	include_spip("inc/presentation");
	include_spip('public/assembler');

	$retour = _request('retour');

	$id_visite = intval(_request('id_visite'));
	
	
	if ($id_visite){
		$result = spip_query("SELECT * FROM spip_visites_virtuelles WHERE id_visite="._q($id_visite));
		if ($row = spip_fetch_array($result)) {
			$id_lieu = $row['id_lieu'];
			$titre = $row['titre'];
			
		
		}
	}
	
	debut_page("&laquo; $titre &raquo;", "documents", "visitesvirtuelles","");

	
	
	// gauche raccourcis ---------------------------------------------------------------
	debut_gauche();
	
	debut_boite_info();
	if ($id_lieu>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:visite_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_visite</div></div>\n";
		icone_horizontale(_T('icone_retour'), "?exec=visitesvirtuelles_toutes", "../"._DIR_PLUGIN_PANORAMAS."img_pack/planet_costea_bogdan_r.png", "rien.gif",'right');
	
	fin_boite_info();
	
	// droite ---------------------------------------------------------------
	creer_colonne_droite();
	debut_droite();

	
	$out = "";
	
	// centre previsualisation ---------------------------------------------------------------
	$out .= "<div id='previsualisation'>";
	$GLOBALS['var_mode']='calcul';
	$out .= recuperer_fond('modeles/visite', array('id_visite'=>$id_visite,'var_mode'=>'calcul'));
	$out .= "</div>";

	echo $out;

	
	

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}


?>
