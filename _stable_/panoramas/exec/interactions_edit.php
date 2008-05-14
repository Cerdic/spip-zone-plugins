<?php

include_spip('inc/panoramas_edit');
	

function Panoramas_interaction_confirme_suppression($id_interaction,$redirect,$retour){
	global $spip_lang_right;
	$out = "<div class='verdana3'>";
	$out .= "<br />";
	$out .= _T("panoramas:info_supprimer_interaction")."</p>\n";
	
	$link = generer_action_auteur('interactions_supprime',"$id_interaction",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('Interactions_toutes',"$id_lieu",false,true)));
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


function exec_interactions_edit(){
	
	$retour = _request('retour');

	$id_visite = intval(_request('id_visite'));
	$id_lieu = intval(_request('id_lieu'));
	$id_interaction = intval(_request('id_interaction'));
	
	$new = _request('new');
	
	$supp_interaction = intval(_request('supp_interaction'));
	$supp_rejet = _request('supp_rejet');
	$titre = _request('titre');
	
	if ($supp_interaction) {
		$id_interaction = $supp_interaction;
	}
	if ($retour)
		$retour = urldecode($retour);
	else 
		$retour = generer_url_ecrire('interactions_toutes',"$id_lieu","",true);
  
	include_spip("inc/presentation");
	include_spip("inc/config");

	$redirect = generer_url_ecrire('interactions_edit',(intval($id_lieu)?"id_lieu=$id_lieu":""));
	if ($retour) 
		$redirect = parametre_url($redirect,"retour",urlencode($retour));
		
	//
	// Affichage de la page
	//
	if ($id_interaction){
		$result = spip_query("SELECT * FROM spip_visites_virtuelles_interactions WHERE id_interaction="._q($id_interaction));
		if ($row = spip_fetch_array($result)) {
			$id_lieu = $row['id_lieu'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$id_lieu = $row['id_lieu'];
			$id_visite = $row['id_visite'];
			$x1 = $row['x1'];
			$x2 = $row['x2'];
			$y1 = $row['y1'];
			$y2 = $row['y2'];
			$type = $row['type'];
			$x_lieu_cible = $row['x_lieu_cible'];
			$id_article_cible = $row['id_article_cible'];
			$id_rubrique_cible = $row['id_rubrique_cible'];
			$id_lieu_cible = $row['id_lieu_cible'];
			$id_document_cible = $row['id_document_cible'];
			$id_visite_cible = $row['id_visite_cible'];
			$id_jeu_cible = $row['id_jeu_cible'];
			$url_cible = $row['url_cible'];
			$id_objet = $row['id_objet'];
			$id_personnage = $row['id_personnage'];
			$id_objet_activation = $row['id_objet_activation'];
			$id_personnage_survol = $row['id_personnage_survol'];
			$texte_avant_activation = $row['texte_avant_activation'];
			$texte_apres_activation = $row['texte_apres_activation'];
			$id_audio_avant_activation = $row['id_audio_avant_activation'];
			$id_audio_apres_activation = $row['id_audio_apres_activation'];
	
		}
		$focus = "";
		$action_link = generer_action_auteur("interactions_edit","$id_interaction",urlencode($redirect));
	}
	if ($new) {
		$action_link_lieu = generer_action_auteur("interactions_edit?id_visite=".$id_visite."&id_lieu=".$id_lieu."&id_interaction=new","",urlencode($redirect));
	}
			
	
	debut_page("&laquo; $titre &raquo;", "documents", "interactions","");

	// Recupere les donnees ---------------------------------------------------------------
	if ($new == 'oui' && !$titre) {
		
		$titre = _T("panoramas:nouvelle_interaction");
		include_spip('inc/charset');
		$row['titre'] = $titre = unicode2charset(html2unicode($titre));
		$row['descriptif'] = "";
		$row['id_visite'] = $id_visite;
		$row['id_lieu'] = $id_lieu;
		$row['x1'] = $x1;
		$row['x2'] = $x1;
		$row['y1'] = $y1;
		$row['y2'] = $y2;
		$row['type'] = $type;
		$row['x_lieu_cible'] = $x_lieu_cible;
		$row['id_article_cible'] = $id_article_cible;
		$row['id_rubrique_cible'] = $id_rubrique_cible;
		$row['id_lieu_cible'] = $id_lieu_cible;
		$row['id_document_cible'] = $id_document_cible;
		$row['id_visite_cible'] = $id_visite_cible;
		$row['url_cible'] = $url_cible;
		$row['id_objet'] = $id_objet;
		$row['id_personnage'] = $id_personnage;
		$row['id_objet_activation'] = $id_objet_activation;
		$row['id_jeu_cible'] = $id_jeu_cible;
		$row['id_personnage_survol'] = $id_personnage_survol;
		$row['texte_avant_activation'] = $texte_avant_activation;
		$row['texte_apres_activation'] = $texte_apres_activation;
		$row['id_audio_avant_activation'] = $id_audio_avant_activation;
		$row['id_audio_apres_activation'] = $id_audio_apres_activation;
		
		$action_link = generer_action_auteur("interactions_edit","new",urlencode($redirect));
	}
	
	// gauche raccourcis ---------------------------------------------------------------
	debut_gauche();
	
	debut_boite_info();
	if ($id_lieu>0)
		echo "<div align='center' style='font-size:3em;font-weight:bold;'>$id_lieu</div>\n";
	//if ($retour) {
		icone_horizontale(_T('icone_retour'), "?exec=interactions_toutes&id_lieu=".$id_lieu."&id_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/house_gabrielle_nowicki_.png", "rien.gif",'right');
	//}
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('administrer','interaction',$id_interaction)) {
		$nretour = urlencode(self());
		
		$link = parametre_url(self(),'new','');
		$link = parametre_url($link,'supp_interaction', $id_interaction);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('interactions_edit')));
		}
		echo "<p>";
		icone_horizontale(_T("panoramas:supprimer_interaction"), "?exec=interactions_edit&id_interaction=".$id_intercation."&supp_interaction=".$id_interaction."&retour=".$retour, "../"._DIR_PLUGIN_PANORAMAS."img_pack/supprimer-24.png", "rien.gif");
		echo "</p>";
	}
	fin_boite_info();
	
	// droite ---------------------------------------------------------------
	creer_colonne_droite();
	debut_droite();

	if (!$new){
		echo gros_titre($row['titre'],'',false);
	
		if ($supp_interaction && $supp_rejet==NULL)
			echo Panoramas_interaction_confirme_suppression($id_interaction,$redirect,$retour);
		
	}

	$out = "";
	
	// centre proprietes ---------------------------------------------------------------
	$out .= "<div id='proprietes'>";
	$out .= Panoramas_boite_proprietes_interaction($id_interaction, $row, $focus, $action_link, $redirect);
	$out .= "</div>";

	echo $out;

	
	

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

//
// Edition des lieux
//
function Panoramas_boite_proprietes_interaction($id_interaction, $row, $focus, $action_link, $redirect) {
	

	$titre = entites_html($row['titre']);
	$descriptif = entites_html($row['descriptif']);
	$id_visite = intval($row['id_visite']);
	$id_lieu = intval($row['id_lieu']);
	$x1 = $row['x1'];	
	$x2 = $row['x2'];	
	$y1 = $row['y1'];	
	$y2 = $row['y2'];
	$type = $row['type'];
	$x_lieu_cible = $row['x_lieu_cible'];
	$id_article_cible = $row['id_article_cible'];
	$id_lieu_cible = $row['id_lieu_cible'];
	$id_document_cible = $row['id_document_cible'];
	$id_visite_cible = $row['id_visite_cible'];
	$url_cible = $row['url_cible'];
	$id_objet = intval($row['id_objet']);
	$id_personnage = intval($row['id_personnage']);
	$id_objet_activation = intval($row['id_objet_activation']);
	$id_rubrique_cible = intval($row['id_rubrique_cible']);
	$id_jeu_cible = intval($row['id_jeu_cible']);
	$id_personnage_survol = intval($row['id_personnage_survol']);
	$texte_avant_activation = $row['texte_avant_activation'];
	$texte_apres_activation = $row['texte_apres_activation'];
	$id_audio_avant_activation = intval($row['id_audio_avant_activation']);
	$id_audio_apres_activation = intval($row['id_audio_apres_activation']);
	
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
		"<input type='hidden' name='id_visite' value='$id_visite' />" .
		"<input type='hidden' name='id_lieu' value='$id_lieu' />" ;

		
	

	$out .= "<strong><label for='titre_form'>"._T("panoramas:titre_interaction")."</label></strong> "._T('info_obligatoire_02');
	$out .= "<br />";
	$out .= "<input type='text' name='titre' id='titre_interaction' class='formo $focus' ".
		"value=\"".$titre."\" size='40' /><br />\n";

	$out .= "<strong><label for='desc_form'>"._T('info_descriptif')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='descriptif' id='desc_interaction' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $descriptif;
	$out .= "</textarea><br />\n";

	$out .= "<strong><label for='x1_interaction'>"._T("panoramas:x1")."</label></strong> ";
	$out .= "<input type='text' name='x1' id='x1_interaction' class='formo $focus' ".
		"value=\"".$x1."\" size='5' /><br />\n";

	$out .= "<strong><label for='y1_interaction'>"._T("panoramas:y1")."</label></strong> ";
	$out .= "<input type='text' name='y1' id='y1_interaction' class='formo $focus' ".
		"value=\"".$y1."\" size='5' /><br />\n";

	$out .= "<strong><label for='x2_interaction'>"._T("panoramas:x2")."</label></strong> ";
	$out .= "<input type='text' name='x2' id='x2_interaction' class='formo $focus' ".
		"value=\"".$x2."\" size='5' /><br />\n";
	
	$out .= "<strong><label for='y2_interaction'>"._T("panoramas:y2")."</label></strong> ";
	$out .= "<input type='text' name='y2' id='y2_interaction' class='formo $focus' ".
		"value=\"".$y2."\" size='5' /><br />\n";

	$out .= "<strong><label for='type_interaction'>"._T("panoramas:type")."</label></strong> ";
	$out .= "<select name='type' id='type_interaction' class='formo $focus' ".
		"value=\"".$type."\" >";

	//options
	$out .= "	<option value=\"descriptif\"";
	if ($type=="descriptif") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:descriptif")."</option>";

	$out .= "	<option value=\"lieu\"";
	if ($type=="lieu") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:lieu")."</option>";

	$out .= "	<option value=\"visite\"";
	if ($type=="visite") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:visite")."</option>";
	
	$out .= "	<option value=\"article\"";
	if ($type=="article") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:article")."</option>";

	$out .= "	<option value=\"rubrique\"";
	if ($type=="rubrique") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:rubrique")."</option>";

	$out .= "	<option value=\"document\"";
	if ($type=="document") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:document")."</option>";

	$out .= "	<option value=\"jeu\"";
	if ($type=="jeu") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:jeu")."</option>";

	$out .= "	<option value=\"url\"";
	if ($type=="url") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:url")."</option>";

	$out .= "	<option value=\"objet\"";
	if ($type=="objet") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:objet")."</option>";

	$out .= "	<option value=\"personnage\"";
	if ($type=="personnage") $out .= "selected=\"selected\"";
	$out .= " >"._T("panoramas:personnage")."</option>";

	//fin options

	$out .= "</select><br />\n";
	

	

	$out .= "<strong><label for='x_lieu_cible_interaction'>"._T("panoramas:x_lieu_cible")."</label></strong> ";
	$out .= "<input type='text' name='x_lieu_cible' id='x_lieu_cible_interaction' class='formo $focus' ".
		"value=\"".$x_lieu_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_article_cible_interaction'>"._T("panoramas:id_article_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_article_cible' id='id_article_cible_interaction' class='formo $focus' ".
		"value=\"".$id_article_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_rubrique_cible_interaction'>"._T("panoramas:id_rubrique_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_rubrique_cible' id='id_rubrique_cible_interaction' class='formo $focus' ".
		"value=\"".$id_rubrique_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_jeu_cible_interaction'>"._T("panoramas:id_jeu_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_jeu_cible' id='id_jeu_cible_interaction' class='formo $focus' ".
		"value=\"".$id_jeu_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_lieu_cible_interaction'>"._T("panoramas:id_lieu_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_lieu_cible' id='id_lieu_cible_interaction' class='formo $focus' ".
		"value=\"".$id_lieu_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_document_cible_interaction'>"._T("panoramas:id_document_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_document_cible' id='id_document_cible_interaction' class='formo $focus' ".
		"value=\"".$id_document_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_visite_cible_interaction'>"._T("panoramas:id_visite_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_visite_cible' id='id_visite_cible_interaction' class='formo $focus' ".
		"value=\"".$id_visite_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='url_cible_interaction'>"._T("panoramas:url_cible")."</label></strong> ";
	$out .= "<input url_cible='text' name='url_cible' id='url_cible_interaction' class='formo $focus' ".
		"value=\"".$url_cible."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_objet_interaction'>"._T("panoramas:id_objet")."</label></strong> ";
	$out .= "<input type='text' name='id_objet' id='id_objet_interaction' class='formo $focus' ".
		"value=\"".$id_objet."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_personnage_interaction'>"._T("panoramas:id_personnage")."</label></strong> ";
	$out .= "<input type='text' name='id_personnage' id='id_personnage_interaction' class='formo $focus' ".
		"value=\"".$id_personnage."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_personnage_survol_interaction'>"._T("panoramas:id_personnage_survol")."</label></strong> ";
	$out .= "<input type='text' name='id_personnage_survol' id='id_personnage_survol_interaction' class='formo $focus' ".
		"value=\"".$id_personnage_survol."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_objet_activation_interaction'>"._T("panoramas:id_objet_activation")."</label></strong> ";
	$out .= "<input type='text' name='id_objet_activation' id='id_objet_activation_interaction' class='formo $focus' ".
		"value=\"".$id_objet_activation."\" size='5' /><br />\n";

	$out .= "<strong><label for='texte_avant_activation_form'>"._T('texte_avant_activation')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='texte_avant_activation' id='texte_avant_activation_interaction' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $texte_avant_activation;
	$out .= "</textarea><br />\n";

	$out .= "<strong><label for='texte_apres_activation_form'>"._T('texte_apres_activation')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='texte_apres_activation' id='texte_apres_activation_interaction' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $texte_apres_activation;
	$out .= "</textarea><br />\n";

	$out .= "<strong><label for='id_audio_avant_activation_interaction'>"._T("panoramas:id_audio_avant_activation")."</label></strong> ";
	$out .= "<input type='text' name='id_audio_avant_activation' id='id_audio_avant_activation_interaction' class='formo $focus' ".
		"value=\"".$id_audio_avant_activation."\" size='5' /><br />\n";

	$out .= "<strong><label for='id_audio_apres_activation_interaction'>"._T("panoramas:id_audio_apres_activation")."</label></strong> ";
	$out .= "<input type='text' name='id_audio_apres_activation' id='id_audio_apres_activation_interaction' class='formo $focus' ".
		"value=\"".$id_audio_apres_activation."\" size='5' /><br />\n";

	$out .= "<div style='text-align:right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	$out .= "</form>";
	$out .= "</div>";
	

	$out .= Panoramas_fin_cadre_formulaire(true);
	$out .= "</p>";
	return $out;


}

?>
