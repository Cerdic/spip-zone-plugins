<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
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
		$result = sql_select('*', 'spip_visites_virtuelles_interactions', "id_interaction="._q($id_interaction));
		if ($row = sql_fetch($result)) {
			$id_lieu = $row['id_lieu'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$id_lieu = $row['id_lieu'];
			$id_visite = $row['id_visite'];
			$x1 = $row['x1'];
			$x2 = $row['x2'];
			$y1 = $row['y1'];
			$y2 = $row['y2'];
			$id_image_fond = $row['id_image_fond'];
			$id_image_fond_survol = $row['id_image_fond_survol'];
			$type = $row['type'];
			$x_lieu_cible = $row['x_lieu_cible'];
			$id_article_cible = $row['id_article_cible'];
			$id_rubrique_cible = $row['id_rubrique_cible'];
			$id_lieu_cible = $row['id_lieu_cible'];
			$id_document_cible = $row['id_document_cible'];
			$id_visite_cible = $row['id_visite_cible'];
			$id_jeu_cible = $row['id_jeu_cible'];
			$id_objet_recompense = $row['id_objet_recompense'];
			$url_cible = $row['url_cible'];
			$id_objet_activation = $row['id_objet_activation'];
			$id_jeu_activation = $row['id_jeu_activation'];
			$id_lieu_activation = $row['id_lieu_activation'];
			$texte_avant_activation = $row['texte_avant_activation'];
			$texte_apres_activation = $row['texte_apres_activation'];
			$id_audio_avant_activation = $row['id_audio_avant_activation'];
			$id_audio_apres_activation = $row['id_audio_apres_activation'];
			$id_objet_apres_activation = $row['id_objet_apres_activation'];
			$images_transition = $row['images_transition'];
			$images_transition_delai = $row['images_transition_delai'];
			$id_film_transition = $row['id_film_transition'];
			$film_transition_duree = $row['film_transition_duree'];
			$nb_points_objet = $row['nb_points_objet'];
	
		}
		$focus = "";
		$action_link = generer_action_auteur("interactions_edit","$id_interaction",urlencode($redirect));
	}
	if ($new) {
		$action_link_lieu = generer_action_auteur("interactions_edit?id_visite=".$id_visite."&id_lieu=".$id_lieu."&id_interaction=new","",urlencode($redirect));
	}
			
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("&laquo; $titre &raquo;","documents","interactions");
	

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
		$row['id_image_fond'] = $id_image_fond;
		$row['id_image_fond_survol'] = $id_image_fond_survol;
		$row['type'] = $type;
		$row['x_lieu_cible'] = $x_lieu_cible;
		$row['id_article_cible'] = $id_article_cible;
		$row['id_rubrique_cible'] = $id_rubrique_cible;
		$row['id_lieu_cible'] = $id_lieu_cible;
		$row['id_document_cible'] = $id_document_cible;
		$row['id_visite_cible'] = $id_visite_cible;
		$row['url_cible'] = $url_cible;
		$row['id_objet_activation'] = $id_objet_activation;
		$row['id_lieu_activation'] = $id_lieu_activation;
		$row['id_jeu_activation'] = $id_jeu_activation;
		$row['id_jeu_cible'] = $id_jeu_cible;
		$row['id_objet_recompense'] = $id_objet_recompense;
		$row['texte_avant_activation'] = $texte_avant_activation;
		$row['texte_apres_activation'] = $texte_apres_activation;
		$row['id_audio_avant_activation'] = $id_audio_avant_activation;
		$row['id_audio_apres_activation'] = $id_audio_apres_activation;
		$row['id_objet_apres_activation'] = $id_objet_apres_activation;
		$row['images_transition'] = $images_transition;
		$row['images_transition_delai'] = $images_transition_delai;
		$row['id_film_transition'] = $id_film_transition;
		$row['film_transition_duree'] = $film_transition_duree;
		$row['nb_points_objet'] = $nb_points_objet;
		
		$action_link = generer_action_auteur("interactions_edit","new",urlencode($redirect));
	}
	
	// gauche raccourcis ---------------------------------------------------------------
	echo debut_gauche('', true);
	
	echo debut_boite_info(true);
	if ($id_lieu>0)
		echo "<div align='center' style='font-size:3em;font-weight:bold;'>$id_lieu</div>\n";
	//if ($retour) {
		echo icone_horizontale(_T('icone_retour'), "?exec=interactions_toutes&id_lieu=".$id_lieu."&id_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/house_gabrielle_nowicki_.png", "rien.gif",'', false);
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
		echo icone_horizontale(_T("panoramas:supprimer_interaction"), "?exec=interactions_edit&id_interaction=".$id_interaction."&supp_interaction=".$id_interaction."&retour=".$retour, "../"._DIR_PLUGIN_PANORAMAS."img_pack/supprimer-24.png", "rien.gif", '', false);
		echo "</p>";
	}
	echo fin_boite_info(true);
	
	// droite ---------------------------------------------------------------
	echo creer_colonne_droite(true);
	echo debut_droite('', true);

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
	$id_image_fond = $row['id_image_fond'];
	$id_image_fond_survol = $row['id_image_fond_survol'];
	$type = $row['type'];
	$x_lieu_cible = $row['x_lieu_cible'];
	$id_article_cible = $row['id_article_cible'];
	$id_lieu_cible = $row['id_lieu_cible'];
	$id_document_cible = $row['id_document_cible'];
	$id_visite_cible = $row['id_visite_cible'];
	$url_cible = $row['url_cible'];
	$id_objet_activation = intval($row['id_objet_activation']);
	$id_lieu_activation = intval($row['id_lieu_activation']);
	$id_jeu_activation = intval($row['id_jeu_activation']);
	$id_rubrique_cible = intval($row['id_rubrique_cible']);
	$id_jeu_cible = intval($row['id_jeu_cible']);
	$id_objet_recompense = intval($row['id_objet_recompense']);
	$texte_avant_activation = $row['texte_avant_activation'];
	$texte_apres_activation = $row['texte_apres_activation'];
	$id_audio_avant_activation = intval($row['id_audio_avant_activation']);
	$id_audio_apres_activation = intval($row['id_audio_apres_activation']);
	$id_objet_apres_activation = intval($row['id_objet_apres_activation']);
	$images_transition = $row['images_transition'];
	$images_transition_delai = intval($row['images_transition_delai']);
	$id_film_transition = intval($row['id_film_transition']);
	$film_transition_duree = intval($row['film_transition_duree']);
	$nb_points_objet = intval($row['nb_points_objet']);
	
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

	$resultlieu = sql_query("SELECT * FROM spip_visites_virtuelles_lieux WHERE id_lieu="._q($id_lieu));
		if ($rowlieu = spip_fetch_array($resultlieu)) {
			$id_photo = $rowlieu['id_photo'];
			$resultdocument = sql_query("SELECT * FROM spip_documents WHERE id_document="._q($id_photo));
			if ($rowdocument = sql_fetch($resultdocument)) {
				$fichier = $rowdocument['fichier'];
				$largeur = $rowdocument['largeur'];
				$hauteur = $rowdocument['hauteur'];
				$out .= "<div>
					<img id='panorama-selection-interaction' src='../IMG/".$fichier."' width='".$largeur."' height='".$hauteur."' />
				</div>
				";
			}
			
		}
		
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

	$out .= panorama_afficher_bloc_document("id_image_fond", "interaction", $id_image_fond);
	
	$out .= panorama_afficher_bloc_document("id_image_fond_survol", "interaction", $id_image_fond_survol);
	
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
	

	$out .= "<fieldset id='infos-cible'><legend>"._T("panoramas:informations_cible")."</legend>";

	$out .= "<strong><label for='x_lieu_cible_interaction' id='x_lieu_cible_interaction_label'>"._T("panoramas:x_lieu_cible")."</label></strong> ";
	$out .= "<input type='text' name='x_lieu_cible' id='x_lieu_cible_interaction' class='formo $focus' ".
		"value=\"".$x_lieu_cible."\" size='5' />\n";

	$out .= "<strong><label for='id_article_cible_interaction' id='id_article_cible_interaction_label'>"._T("panoramas:id_article_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_article_cible' id='id_article_cible_interaction' class='formo $focus' ".
		"value=\"".$id_article_cible."\" size='5' />\n";

	$out .= "<strong><label for='id_rubrique_cible_interaction' id='id_rubrique_cible_interaction_label'>"._T("panoramas:id_rubrique_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_rubrique_cible' id='id_rubrique_cible_interaction' class='formo $focus' ".
		"value=\"".$id_rubrique_cible."\" size='5' />\n";

	$out .= "<strong><label for='id_jeu_cible_interaction' id='id_jeu_cible_interaction_label'>"._T("panoramas:id_jeu_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_jeu_cible' id='id_jeu_cible_interaction' class='formo $focus' ".
		"value=\"".$id_jeu_cible."\" size='5' />\n";

	$out .= "<strong><label for='nb_points_objet_interaction' id='nb_points_objet_interaction_label'>"._T("panoramas:nb_points_objet")."</label></strong> ";
	$out .= "<input type='text' name='nb_points_objet' id='nb_points_objet_interaction' class='formo $focus' ".
		"value=\"".$nb_points_objet."\" size='5' />\n";

	$out .= "<strong><label for='id_objet_recompense_interaction' id='id_objet_recompense_interaction_label'>"._T("panoramas:id_objet_recompense")."</label></strong> ";
	$out .= "<input type='text' name='id_objet_recompense' id='id_objet_recompense_interaction' class='formo $focus' ".
		"value=\"".$id_objet_recompense."\" size='5' />\n";

	$out .= "<strong><label for='id_lieu_cible_interaction' id='id_lieu_cible_interaction_label'>"._T("panoramas:id_lieu_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_lieu_cible' id='id_lieu_cible_interaction' class='formo $focus' ".
		"value=\"".$id_lieu_cible."\" size='5' />\n";

	$out .= "<strong><label for='id_document_cible_interaction' id='id_document_cible_interaction_label'>"._T("panoramas:id_document_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_document_cible' id='id_document_cible_interaction' class='formo $focus' ".
		"value=\"".$id_document_cible."\" size='5' />\n";

	$out .= "<strong><label for='id_visite_cible_interaction' id='id_visite_cible_interaction_label'>"._T("panoramas:id_visite_cible")."</label></strong> ";
	$out .= "<input type='text' name='id_visite_cible' id='id_visite_cible_interaction' class='formo $focus' ".
		"value=\"".$id_visite_cible."\" size='5' />\n";

	$out .= "<strong><label for='url_cible_interaction' id='url_cible_interaction_label'>"._T("panoramas:url_cible")."</label></strong> ";
	$out .= "<input url_cible='text' name='url_cible' id='url_cible_interaction' class='formo $focus' ".
		"value=\"".$url_cible."\" size='5' />\n";

	$out .= "</fieldset>";

	$out .= "<fieldset id='infos-activation'><legend>"._T("panoramas:informations_activation")."</legend>";

	$out .= "<strong><label for='id_objet_activation_interaction' id='id_objet_activation_interaction_label'>"._T("panoramas:id_objet_activation")."</label></strong> ";
	$out .= "<input type='text' name='id_objet_activation' id='id_objet_activation_interaction' class='formo $focus' ".
		"value=\"".$id_objet_activation."\" size='5' />\n";

	$out .= "<strong><label for='id_jeu_activation_interaction' id='id_jeu_activation_interaction_label'>"._T("panoramas:id_jeu_activation")."</label></strong> ";
	$out .= "<input type='text' name='id_jeu_activation' id='id_jeu_activation_interaction' class='formo $focus' ".
		"value=\"".$id_jeu_activation."\" size='5' />\n";

	$out .= "<strong><label for='id_lieu_activation_interaction' id='id_lieu_activation_interaction_label'>"._T("panoramas:id_lieu_activation")."</label></strong> ";
	$out .= "<input type='text' name='id_lieu_activation' id='id_lieu_activation_interaction' class='formo $focus' ".
		"value=\"".$id_lieu_activation."\" size='5' />\n";

	$out .= "<strong><label for='texte_avant_activation_form' id='texte_avant_activation_interaction_label'>"._T('texte_avant_activation')."</label></strong>";
	$out .= "<textarea name='texte_avant_activation' id='texte_avant_activation_interaction' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $texte_avant_activation;
	$out .= "</textarea>\n";

	$out .= "<strong><label for='texte_apres_activation_form' id='texte_apres_activation_interaction_label'>"._T('texte_apres_activation')."</label></strong>";
	$out .= "<textarea name='texte_apres_activation' id='texte_apres_activation_interaction' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= $texte_apres_activation;
	$out .= "</textarea>\n";

	$out .= panorama_afficher_bloc_document("id_audio_avant_activation", "interaction", $id_audio_avant_activation);
	
	$out .= panorama_afficher_bloc_document("id_audio_apres_activation", "interaction", $id_audio_apres_activation);
	
	$out .= panorama_afficher_bloc_document("id_objet_apres_activation", "interaction", $id_objet_apres_activation);
	
	$out .= "</fieldset>";
	
	//s�lection du mode transition
	$out .= "<fieldset id='infos-transition'><legend>"._T("panoramas:informations_transition")."</legend>";
	
	$out .= panorama_afficher_bloc_document("images_transition", "interaction", $images_transition);
	
	$out .= "<strong><label for='images_transition_delai_interaction' id='images_transition_delai_interaction_label'>"._T("panoramas:images_transition_delai")."</label></strong> ";
	$out .= "<input type='text' name='images_transition_delai' id='images_transition_delai_interaction' class='formo $focus' ".
		"value=\"".$images_transition_delai."\" size='5' />\n";

	$out .= panorama_afficher_bloc_document("id_film_transition", "interaction", $id_film_transition);
	
	$out .= "<strong><label for='film_transition_duree' id='film_transition_duree_interaction_label'>"._T("panoramas:film_transition_duree")."</label></strong> ";
	$out .= "<input type='text' name='film_transition_duree' id='film_transition_duree_interaction' class='formo $focus' ".
		"value=\"".$film_transition_duree."\" size='5' />\n";

	$out .= "</fieldset>";

	$out .= "<div style='text-align:right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	$out .= "</form>";
	$out .= "</div>";
	$out .= "<script type='text/javascript'>  
			function selectionEnd(img, selection) { 
				$('#x1_interaction').val(selection.x1);
				$('#x2_interaction').val(selection.x2);
				$('#y1_interaction').val(selection.y1);
				$('#y2_interaction').val(selection.y2);

			}
			function switch_infos_cible() {
				$('#infos-cible input, #infos-cible textarea, #infos-cible label').hide();
					switch ($('#type_interaction option:selected').val()) {
						case 'article' : $('#id_article_cible_interaction, #id_article_cible_interaction_label').show(); break;
						case 'lieu' : $('#id_lieu_cible_interaction, #id_lieu_cible_interaction_label, #x_lieu_cible_interaction, #x_lieu_cible_interaction_label').show(); break;
						case 'visite' : $('#id_visite_cible_interaction, #id_visite_cible_interaction_label').show(); break;
						case 'jeu' : $('#id_jeu_cible_interaction, #id_objet_recompense_interaction, #id_objet_recompense_interaction_label, #id_jeu_cible_interaction_label').show(); break;
						case 'rubrique' : $('#id_rubrique_cible_interaction, #id_rubrique_cible_interaction_label').show(); break;
						case 'document' : $('#id_document_cible_interaction, #id_document_cible_interaction_label').show(); break;
						case 'url' : $('#url_cible_interaction, #url_cible_interaction_label').show(); break;
						case 'objet' : $('#nb_points_objet_interaction, #nb_points_objet_interaction_label').show(); break;
						case 'personnage' : break;
						case 'descriptif' : break;
						default : $('#infos-cible input, #infos-cible textarea, #infos-cible label').show();
					}
			}
			$(document).ready(function () { 
				var initialx1 = parseInt($('#x1_interaction').val());
				var initialy1 = parseInt($('#y1_interaction').val());
				var initialx2 = parseInt($('#x2_interaction').val());
				var initialy2 = parseInt($('#y2_interaction').val());
				
				$('img#panorama-selection-interaction').imgAreaSelect({
						selectionColor: 'blue', 
						onSelectEnd: selectionEnd, 
						x1: initialx1, 
						y1: initialy1, 
						x2: initialx2, 
						y2: initialy2
				});

				//affichage des zones � renseigner en fonction du type choisi
				$('#type_interaction').bind('change', function () {
					switch_infos_cible();
					
				});
				switch_infos_cible();
			}); 
		
		</script>";
	

	$out .= Panoramas_fin_cadre_formulaire(true);
	$out .= "</p>";
	return $out;


}

?>
