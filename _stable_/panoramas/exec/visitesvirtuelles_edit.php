<?php

include_spip('inc/panoramas_edit');
	

function Panoramas_visitevirtuelle_confirme_suppression($id_visite,$nb_lieux,$redirect,$retour){
	global $spip_lang_right;
	$out = "<div class='verdana3'>";
	if ($nb_lieux){
			$out .= "<p><strong>"._T("panoramas:attention_visite")."</strong> ";
			$out .= _T("panoramas:info_supprimer_visite")."</p>\n";
	}
	else{
		$out .= "<br />";
		$out .= _T("panoramas:info_supprimer_visite")."</p>\n";
	}
	$link = generer_action_auteur('visitesvirtuelles_supprime',"$id_visite",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('visitesvirtuelles_toutes',"",false,true)));
	$out .= "<form method='POST' action='$link' style='float:$spip_lang_right'>";
	$out .= form_hidden($link);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "&nbsp;<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form>\n";

	$out .= "<form method='POST' action='$redirect' style='float:$spip_lang_right'>\n";
	$out .= form_hidden($redirect);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "&nbsp;<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form><br />\n";
	$out .= "</div>";

	return $out;
}


function exec_visitesvirtuelles_edit(){
	
	$retour = _request('retour');

	$id_visite = intval(_request('id_visite'));
	
	$new = _request('new');
	
	$supp_visite = intval(_request('supp_visite'));
	$supp_rejet = _request('supp_rejet');
	$titre = _request('titre');
	
	if ($supp_visite) {
		$id_visite = $supp_visite;
	}
	if ($retour)
		$retour = urldecode($retour);
	else 
		$retour = generer_url_ecrire('visitesvirtuelles_toutes',"","",true);
  
	include_spip("inc/presentation");
	include_spip("inc/config");

	$nb_lieux = 0;
	if ($id_visite)
		if ($row = spip_fetch_array(spip_query("SELECT COUNT(*) AS num FROM spip_visites_virtuelles_lieux WHERE id_visite="._q($id_visite))))
			$nb_lieux = $row['num'];

	
	$redirect = generer_url_ecrire('visitesvirtuelles_edit',(intval($id_visite)?"id_visite=$id_visite":""));
	if ($retour) 
		$redirect = parametre_url($redirect,"retour",urlencode($retour));
		
	//
	// Affichage de la page
	//
	if ($id_visite){
		$result = spip_query("SELECT * FROM spip_visites_virtuelles WHERE id_visite="._q($id_visite));
		if ($row = spip_fetch_array($result)) {
			$id_visite = $row['id_visite'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$largeur = $row['largeur'];
			$hauteur = $row['hauteur'];
			$id_lieu_depart = $row['id_lieu_depart'];
			$id_carte = $row['id_carte'];
		}
		$focus = "";
		$action_link = generer_action_auteur("visitesvirtuelles_edit","$id_visite",urlencode($redirect));
	}
		
	
	debut_page("&laquo; $titre &raquo;", "documents", "visitesvirtuelles","");

	// Recupere les donnees ---------------------------------------------------------------
	if ($new == 'oui' && !$titre) {
		
		$titre = _T("panoramas:nouvelle_visite");
		include_spip('inc/charset');
		$row['titre'] = $titre = unicode2charset(html2unicode($titre));
		$row['descriptif'] = "";
		
		$action_link = generer_action_auteur("visitesvirtuelles_edit","new",urlencode($redirect));
	}
	
	// gauche raccourcis ---------------------------------------------------------------
	debut_gauche();
	
	debut_boite_info();
	if ($id_visite>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:visite_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_visite</div></div>\n";
	if ($retour) {
		icone_horizontale(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_PANORAMAS."img_pack/logo_panoramas.png", "rien.gif",'right');
	}
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('administrer','visitevirtuelle',$id_visite)) {
		$nretour = urlencode(self());
		
		$link = parametre_url(self(),'new','');
		$link = parametre_url($link,'supp_visite', $id_visite);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('visitesvirtuelles_edit')));
		}
		echo "<p>";
		icone_horizontale(_T("panoramas:supprimer_visite"), "?exec=visitesvirtuelles_edit&supp_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/supprimer-24.png", "rien.gif");
		echo "</p>";
	}
	fin_boite_info();
	
	// droite ---------------------------------------------------------------
	creer_colonne_droite();
	debut_droite();

	if (!$new){
		echo gros_titre($row['titre'],'',false);
	
		if ($supp_visite && $supp_rejet==NULL)
			echo Panoramas_visitevirtuelle_confirme_suppression($id_visite,$nb_lieux,$redirect,$retour);
		
	}

	$out = "";
	
	// centre proprietes ---------------------------------------------------------------
	$out .= "<div id='proprietes'>";
	$out .= Panoramas_boite_proprietes_visitevirtuelle($id_visite, $row, $focus, $action_link, $redirect);
	$out .= "</div>";

	echo $out;

	
	

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

//
// Edition des visites virtuelles
//
function Panoramas_boite_proprietes_visitevirtuelle($id_visite, $row, $focus, $action_link, $redirect) {
	

	$out = "";
	$out .= "<p>";
	$out .= Panoramas_debut_cadre_formulaire('',true);

	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<div class='verdana2'>";
	$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir'" .
		" style='border: 0px; margin: 0px;'>" .
		form_hidden($action_link_noredir) .
		"<input type='hidden' name='redirect' value='$redirect' />" . // form_hidden ne desencode par redirect ...
		"<input type='hidden' name='idtarget' value='proprietes' />" ;

	$titre = entites_html($row['titre']);
	$descriptif = entites_html($row['descriptif']);
	$largeur = intval($row['largeur']);	
	$hauteur = intval($row['hauteur']);
	$id_carte = intval($row['id_carte']);	
	if ($largeur==0) $largeur=600;	
	if ($hauteur==0) $hauteur=400;	
	$id_lieu_depart = intval($row['id_lieu_depart']);	


	$out .= "<strong><label for='titre_visite'>"._T("panoramas:titre_visite")."</label></strong> "._T('info_obligatoire_02');
	$out .= "<br />";
	$out .= "<input type='text' name='titre' id='titre_visite' class='formo $focus' ".
		"value=\"".$titre."\" size='40' /><br />\n";

	$out .= "<strong><label for='desc_visite'>"._T('info_descriptif')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='descriptif' id='desc_visite' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $descriptif;
	$out .= "</textarea><br />\n";

	$out .= "<strong><label for='largeur_visite'>"._T("panoramas:largeur")."</label></strong> ";
	$out .= "<input type='text' name='largeur' id='largeur_visite' class='formo $focus' ".
		"value=\"".$largeur."\" size='5' /><br />\n";
	
	$out .= "<strong><label for='hauteur_visite'>"._T("panoramas:hauteur")."</label></strong> ";
	$out .= "<input type='text' name='hauteur' id='hauteur_visite' class='formo $focus' ".
		"value=\"".$hauteur."\" size='5' /><br />\n";


	$out .= "<strong><label for='id_lieu_depart_visite'>"._T("panoramas:id_lieu_depart")."</label></strong> ";
	$out .= "<input type='text' name='id_lieu_depart' id='id_lieu_depart_visite' class='formo $focus' ".
		"value=\"".$id_lieu_depart."\" size='5' /><br />\n";
	
	$out .= "<strong><label for='id_carte_visite'>"._T("panoramas:id_carte")."</label></strong> ";
	$out .= "<input type='text' name='id_carte' id='id_carte_visite' class='formo $focus' ".
		"value=\"".$id_carte."\" size='5' /><br />\n";
	
	$out .= "<div style='text-align:right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	$out .= "</form>";
	$out .= "</div>";
	

	$out .= Panoramas_fin_cadre_formulaire(true);
	$out .= "</p>";
	return $out;


}




?>
