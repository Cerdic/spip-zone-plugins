<?php

include_spip('inc/panoramas_edit');
	

function Panoramas_lieu_confirme_suppression($id_lieu,$nb_interactions,$redirect,$retour){
	global $spip_lang_right;
	$out = "<div class='verdana3'>";
	if ($nb_interactions){
			$out .= "<p><strong>"._T("panoramas:attention_lieu")."</strong> ";
			$out .= _T("panoramas:info_supprimer_lieu")."</p>\n";
	}
	else{
		$out .= "<br />";
		$out .= _T("panoramas:info_supprimer_lieu")."</p>\n";
	}
	$link = generer_action_auteur('lieux_supprime',"$id_lieu",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('visitesvirtuelles_toutes',"",false,true)));
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


function exec_lieux_edit(){
	
	$retour = _request('retour');

	$id_visite = intval(_request('id_visite'));
	$id_lieu = intval(_request('id_lieu'));
	
	$new = _request('new');
	
	$supp_lieu = intval(_request('supp_lieu'));
	$supp_rejet = _request('supp_rejet');
	$titre = _request('titre');
	
	if ($supp_lieu) {
		$id_lieu = $supp_lieu;
	}
	if ($retour)
		$retour = urldecode($retour);
	else 
		$retour = generer_url_ecrire('lieux_tous',"$id_visite","",true);
  
	include_spip("inc/presentation");
	include_spip("inc/config");

	$nb_interactions = 0;
	if ($id_lieu)
		if ($row = spip_fetch_array(spip_query("SELECT COUNT(*) AS num FROM spip_visites_virtuelles_interactions WHERE id_lieu="._q($id_lieu))))
			$nb_interactions = $row['num'];

	
	$redirect = generer_url_ecrire('lieux_edit',(intval($id_visite)?"id_visite=$id_visite":""));
	if ($retour) 
		$redirect = parametre_url($redirect,"retour",urlencode($retour));
		
	//
	// Affichage de la page
	//
	if ($id_lieu){
		$result = spip_query("SELECT * FROM spip_visites_virtuelles_lieux WHERE id_lieu="._q($id_lieu));
		if ($row = spip_fetch_array($result)) {
			$id_lieu = $row['id_lieu'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$id_visite = $row['id_visite'];
			$boucler = $row['boucler'];
			$id_photo = $row['id_photo'];
			$id_audio = $row['id_audio'];
			$audio_repeter = $row['audio_repeter'];
			$position_x_carte = $row['position_x_carte'];
			$position_y_carte = $row['position_y_carte'];
			$url_carte = $row['url_carte'];
			$decalage_x = $row['decalage_x'];
			$documents_associes = $row['documents_associes'];
		}
		$focus = "";
		$action_link = generer_action_auteur("lieux_edit","$id_lieu",urlencode($redirect));
	}
	if ($new) {
		$action_link_lieu = generer_action_auteur("lieux_edit?id_visite=".$id_visite."&id_lieu=new","",urlencode($redirect));
	}
			
	
	debut_page("&laquo; $titre &raquo;", "documents", "lieux","");

	// Recupere les donnees ---------------------------------------------------------------
	if ($new == 'oui' && !$titre) {
		
		$titre = _T("panoramas:nouveau_lieu");
		include_spip('inc/charset');
		$row['titre'] = $titre = unicode2charset(html2unicode($titre));
		$row['descriptif'] = "";
		$row['id_visite'] = $id_visite;
		$action_link = generer_action_auteur("lieux_edit","new",urlencode($redirect));
	}
	
	// gauche raccourcis ---------------------------------------------------------------
	debut_gauche();
	
	debut_boite_info();
	if ($id_lieu>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:lieu_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_lieu</div></div>\n";
	//if ($retour) {
		icone_horizontale(_T('icone_retour'), "?exec=lieux_tous&id_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/planet_costea_bogdan_r.png", "rien.gif",'right');
	//}
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('administrer','lieu',$id_lieu)) {
		$nretour = urlencode(self());
		
		$link = parametre_url(self(),'new','');
		$link = parametre_url($link,'supp_lieu', $id_lieu);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('lieux_edit')));
		}
		echo "<p>";
		icone_horizontale(_T("panoramas:supprimer_lieu"), "?exec=lieux_edit&id_lieu=".$id_lieu."&supp_lieu=".$id_lieu, "../"._DIR_PLUGIN_PANORAMAS."img_pack/supprimer-24.png", "rien.gif");
		echo "</p>";
	}
	fin_boite_info();
	
	// droite ---------------------------------------------------------------
	creer_colonne_droite();
	debut_droite();

	if (!$new){
		echo gros_titre($row['titre'],'',false);
	
		if ($supp_lieu && $supp_rejet==NULL)
			echo Panoramas_lieu_confirme_suppression($id_lieu,$nb_interactions,$redirect,$retour);
		
	}

	$out = "";
	
	// centre proprietes ---------------------------------------------------------------
	$out .= "<div id='proprietes'>";
	$out .= Panoramas_boite_proprietes_lieu($id_lieu, $row, $focus, $action_link, $redirect);
	$out .= "</div>";

	echo $out;

	
	

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

//
// Edition des lieux
//
function Panoramas_boite_proprietes_lieu($id_lieu, $row, $focus, $action_link, $redirect) {
	

	$id_visite = entites_html($row['id_visite']);
	$titre = entites_html($row['titre']);
	$descriptif = entites_html($row['descriptif']);
	$boucler = $row['boucler'];	
	$audio_repeter = $row['audio_repeter'];	
	$id_photo =  intval($row['id_photo']);
	$id_audio =  intval($row['id_audio']);
	$position_x_carte =  intval($row['position_x_carte']);
	$position_y_carte =  intval($row['position_y_carte']);
	$url_carte =  $row['url_carte'];
	$decalage_x =  intval($row['decalage_x']);
	$documents_associes =  $row['documents_associes'];
	
	$out = "";
	$out .= "<p>";
	$out .= Panoramas_debut_cadre_formulaire('',true);

	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<div class='verdana2'>";
	$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir'" .
		" style='border: 0px; margin: 0px;'>" .
		form_hidden($action_link_noredir) .
		"<input type='hidden' name='redirect' value='$redirect' />" . // form_hidden ne desencode par redirect ...
		"<input type='hidden' name='idtarget' value='proprietes' />" .
		"<input type='hidden' name='id_visite' value='$id_visite' />" ;

	$out .= "<strong><label for='titre_lieu'>"._T("panoramas:titre_lieu")."</label></strong> "._T('info_obligatoire_02');
	$out .= "<br />";
	$out .= "<input type='text' name='titre' id='titre_lieu' class='formo $focus' ".
		"value=\"".$titre."\" size='40' /><br />\n";

	$out .= "<strong><label for='desc_lieu'>"._T('info_descriptif')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='descriptif' id='desc_lieu' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $descriptif;
	$out .= "</textarea><br />\n";

	$out .= "<strong><label for='boucler_lieu'>"._T("panoramas:boucler")."</label></strong> ";
	$out .= "<select name='boucler' id='boucler_lieu' class='formo $focus' ".
		"value=\"".$boucler."\" >
			<option value=\"oui\"";
	if ($boucler=="oui") $out .= "selected=\"selected\"";
	

	$out .= " >"._T("panoramas:oui")."</option>
			<option value=\"non\"";

	if (!($boucler=="oui")) $out .= "selected=\"selected\"";

	$out .= " >"._T("panoramas:non")."</option>
		</select><br />\n";
	$out .= "<strong><label for='id_photo_lieu'>"._T("panoramas:id_photo")."</label></strong> ";
	$out .= "<input type='text' name='id_photo' id='id_photo_lieu' class='formo $focus' ".
		"value=\"".$id_photo."\" size='5' /><br />\n";

	$out .= "<strong><label for='decalage_x_lieu'>"._T("panoramas:decalage_x")."</label></strong> ";
	$out .= "<input type='text' name='decalage_x' id='decalage_x_lieu' class='formo $focus' ".
		"value=\"".$decalage_x."\" size='5' /><br />\n";

	
	$out .= "<strong><label for='audio_repeter_lieu'>"._T("panoramas:audio_repeter")."</label></strong> ";
	$out .= "<select name='audio_repeter' id='audio_repeter_lieu' class='formo $focus' ".
		"value=\"".$audio_repeter."\" >
			<option value=\"oui\"";
	if ($audio_repeter=="oui") $out .= "selected=\"selected\"";
	

	$out .= " >"._T("panoramas:oui")."</option>
			<option value=\"non\"";

	if (!($audio_repeter=="oui")) $out .= "selected=\"selected\"";

	$out .= " >"._T("panoramas:non")."</option>
		</select><br />\n";
	

	$out .= "<strong><label for='id_audio_lieu'>"._T("panoramas:id_audio")."</label></strong> ";
	$out .= "<input type='text' name='id_audio' id='id_audio_lieu' class='formo $focus' ".
		"value=\"".$id_audio."\" size='5' /><br />\n";

	$out .= "<strong><label for='position_x_carte_lieu'>"._T("panoramas:position_x_carte")."</label></strong> ";
	$out .= "<input type='text' name='position_x_carte' id='position_x_carte_lieu' class='formo $focus' ".
		"value=\"".$position_x_carte."\" size='5' /><br />\n";

	$out .= "<strong><label for='position_y_carte_lieu'>"._T("panoramas:position_y_carte")."</label></strong> ";
	$out .= "<input type='text' name='position_y_carte' id='position_y_carte_lieu' class='formo $focus' ".
		"value=\"".$position_y_carte."\" size='5' /><br />\n";

	$out .= "<strong><label for='url_carte_lieu'>"._T("panoramas:url_carte")."</label></strong> ";
	$out .= "<input type='text' name='url_carte' id='url_carte_lieu' class='formo $focus' ".
		"value=\"".$url_carte."\" size='5' /><br />\n";

	$out .= "<strong><label for='documents_associes_lieu'>"._T("panoramas:documents_associes")."</label></strong> ";
	$out .= "<input type='text' name='documents_associes' id='documents_associes_lieu' class='formo $focus' ".
		"value=\"".$documents_associes."\" size='5' /><br />\n";

	$out .= "<div style='text-align:right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	$out .= "</form>";
	$out .= "</div>";
	

	$out .= Panoramas_fin_cadre_formulaire(true);
	$out .= "</p>";
	return $out;


}

?>
