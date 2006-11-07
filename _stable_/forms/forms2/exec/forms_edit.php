<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms');
include_spip('inc/forms_edit');
include_spip('inc/forms_type_champs'); // gestion des types de champs

function debut_block_route($id,$visible){
	$display = $visible?'block':'none';
	return "<script type='text/javascript'><!--
					document.write('<div id=\"$id\" style=\"display: $display; margin-top: 1;\">');
					//--></script>
					<noscript>
					<div id='bock_email_route_$code' style='display: block;'>
					</noscript>";	
}
function fin_block_route(){
	return 	"<script type='text/javascript'><!--
					document.write('<\/div>');
					//--></script>
					<noscript>
						</div>
					</noscript>";
}
function Forms_bloc_routage_mail($id_form,$email){
		$out = "";
		// Routage facultatif des emails en fonction d'un champ select
		$email_route_known = false;
		$jshide = "";
		$s = "";
		$options = "";
		$res2 = spip_query("SELECT * FROM spip_forms_champs WHERE type='select' AND id_form="._q($id_form));
		while ($row2 = spip_fetch_array($res2)) {
			$visible = false;
			$code = $row2['champ'];
			$options .= "<option value='$code'";
			if ($email['route'] == $code){
				$options .= " selected='selected'";
				$email_route_known = $visible = true;
			}
			$options .= ">" . $row2['titre'] . "</option>\n";
			$s .= debut_block_route("bock_email_route_$code",$visible);
			$jshide .=  "cacher_email_route('bock_email_route_$code');\n";
			
			$s .= "<table id ='email_route_$code'>\n";
			$s .= "<tr><th>".$row2['titre']."</th><th>";
			$s .= "<strong><label for='email_route_$code'>"._T('email_2')."</label></strong>";
			$s .= "</th></tr>\n";
			$js = "";

			$res3 = spip_query("SELECT * FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($row2['champ']));
			while($row3 = spip_fetch_array($res3)){
				$s .= "<tr><td>".$row3['titre']."</td><td>";
				$s .= "<input type='text' name='email[".$row3['choix']."]' value=\"";
				$s .= isset($email[$row3['choix']])?entites_html($email[$row3['choix']]):"";
				$s .= "\" class='fondl verdana2' size='20'$js>";
				$s .= "</td></tr>";
			}
			$s .="</table>";
			$s .= fin_block_route("bock_email_route_$code",$visible);
		}
		if (strlen($s)){
			$jshide = "<script type='text/javascript'><!--
			function montrer_email_route(obj) {
				layer = findObj(obj);
				if (layer)
					layer.style.display = 'block';
			}
			function cacher_email_route(obj) {
				layer = findObj(obj);
				if (layer)
					layer.style.display = 'none';
			}
			function update_email_route_visibility(obj){
				$jshide
				cacher_email_route('bock_email_route_');
				montrer_email_route(obj);
			}
			//--></script>\n";
			$out .= $jshide;

			$out .= "<strong><label for='email_route_form'>"._T('forms:choisir_email')."</label></strong> ";
			$out .= "<br />";
			$out .= "<select name='email[route]' id='email_route_form' class='forml'";
			$out .= "onchange='update_email_route_visibility(\"bock_email_route_\"+options[selectedIndex].value)' ";
			$out .= ">\n";
			$out .= "<option value=''>"._T('forms:email_independant')."</option>\n";
			$out .= $options;
		 	$out .= "</select><br />\n";
		}
	 	
		$out .= debut_block_route("bock_email_route_",$email_route_known==false);
		$out .= "<strong><label for='email_form'>"._T('email_2')."</label></strong> ";
		$out .= "<br />";
		$out .= "<input type='text' name=\"email[defaut]\" id='email_form' class='forml' ".
			"value=\"".entites_html($email['defaut'])."\" size='40'$js_titre>\n";
		$out .= fin_block_route();
	 	$out .= $s;
		$out .= "<br/>";
		return $out;
}

function Forms_bloc_edition_champ($row, $action_link) {
	global $couleur_claire;

	$champ = $row['champ'];
	$type = $row['type'];
	$titre = $row['titre'];
	$obligatoire = $row['obligatoire'];
	$extra_info = $row['extra_info'];
	$specifiant = $row['specifiant'];
	$public = $row['public'];
	$aide = $row['aide'];
	$html_wrap = $row['html_wrap'];
	
	$out = "";

	if ($type != 'separateur'){
		$checked = ($obligatoire == 'oui') ? " checked='checked'" : "";
		$out .= "&nbsp; &nbsp; <input type='checkbox' name='champ_obligatoire' value='oui' id='obli_$champ'$checked> ";
		$out .= "<label for='obli_$champ'>"._T("forms:edit_champ_obligatoire")."</label>";
		$out .= "<br />\n";
		
		$checked = ($specifiant == 'oui') ? " checked='checked'" : "";
		$out .= "&nbsp; &nbsp; <input type='checkbox' name='champ_specifiant' value='oui' id='spec_$champ'$checked> ";
		$out .= "<label for='spec_$champ'>"._T("forms:champ_specifiant")."</label>";
		$out .= "<br />\n";
	}
	$checked = ($public == 'oui') ? " checked='checked'" : "";
	$out .= "&nbsp; &nbsp; <input type='checkbox' name='champ_public' value='oui' id='public_$champ'$checked> ";
	$out .= "<label for='public_$champ'>"._T("forms:champ_public")."</label>";
	$out .= "<br />\n";

	if ($type == 'url') {
		$checked = ($extra_info == 'oui') ? " checked='checked'" : "";
		$out .= "&nbsp; &nbsp; <input type='checkbox' name='champ_verif' value='oui' id='verif_$champ'$checked> ";
		$out .= "<label for='verif_$champ'>"._T("forms:verif_web")."</label>";
		$out .= "<br />\n";
	}
	if ($type == 'select' || $type == 'multiple') {
		global $ajout_choix;

		$out .= "<div style='margin: 5px; padding: 5px; border: 1px dashed $couleur_claire;'>";
		$out .= _T("forms:liste_choix")."&nbsp;:<br />\n";
		$res2 = spip_query("SELECT * FROM spip_forms_champs_choix WHERE champ="._q($champ));
		while ($row2 = spip_fetch_array($res2)){
			$choix = $row2['choix'];
			if ($ajout_choix == $choix) {
				$out .= "<script type='text/javascript'><!-- \nvar antifocus_choix= false; // --></script>\n";
				$js = " onfocus=\"if(!antifocus_choix){this.value='';antifocus_choix=true;}\"";
			}
			else $js = "";
			$out .= "<input type='text' id='nom_$choix' name='$choix' value=\"".entites_html($row2['titre'])."\" ".
				"class='fondl verdana2' size='20'$js>";
			// 
			$out .= " <input style='display: none;' type='submit' name='modif_choix' value=\""._T('bouton_modifier')."\" class='fondo verdana2'>";
			$supp_link = parametre_url($action_link,'supp_choix', $choix);
			$out .= " &nbsp; <span class='verdana1'>[<a href='".$supp_link."#champ_visible'>".
				_T("forms:supprimer_choix")."</a>]</span>";
			$out .= "<br />\n";
		}
		$out .= "<br /><input type='submit' name='ajout_choix' value=\""._T("forms:ajouter_choix")."\" class='fondo verdana2'>";

		$out .= "</div>";
		if ($type=='select')
			$out .= "<br /><input type='submit' name='switch_select_multi' value=\""._T("forms:changer_choix_multiple")."\" class='fondl verdana2'>";
		if ($type=='multiple')
			$out .= "<br /><input type='submit' name='switch_select_multi' value=\""._T("forms:changer_choix_unique")."\" class='fondl verdana2'>";
		$out .= "<br />\n";
	}
	if ($type == 'mot') {
		$out .= "<label for='groupe_$champ'>"._T("forms:champ_nom_groupe")."</label> :";
		$out .= " &nbsp;<select name='groupe_champ' value='0' id='groupe_$champ' class='fondo verdana2'>\n";
		$res2 = spip_query("SELECT * FROM spip_groupes_mots ORDER BY titre");
		while ($row2 = spip_fetch_array($res2)) {
			$id_groupe = $row2['id_groupe'];
			$titre_groupe = supprimer_tags(typo($row2['titre']));
			$selected = ($id_groupe == $row['extra_info']) ? " selected='selected'": "";
			$out .= "<option value='$id_groupe'$selected>$titre_groupe</option>\n";
		}
		$out .= "</select>";
		$out .= "<br />\n";
	}
	if ($type == 'fichier') {
		$taille = intval($row['extra_info']);
		if (!$taille) $taille = '';
		$out .= "<label for='taille_$champ'>"._T("forms:taille_max")."</label> : ";
		$out .= "<input type='text' name='taille_champ' value='$taille' id='taille_$champ' class='fondo verdana2'>\n";
		$out .= "<br />\n";
	}

	return $out;
}

function Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ, $redirect){
	global $spip_lang_right;
	$out = "";
	if (!$id_form) return $out;
	$out .= "<a name='champs'></a>";
	$out .= "<p><hr><p>\n";
	$out .= "<div class='verdana3'>";
	$out .= "<strong>"._T("forms:champs_formulaire")."</strong><br />\n";
	$out .= _T("forms:info_champs_formulaire");
	$out .= "</div>\n";
	$out .= "<div id='forms_lang'></div>";

	if ($row = spip_fetch_array(spip_query("SELECT MAX(rang) AS rangmax, MIN(rang) AS rangmin FROM spip_forms_champs WHERE id_form="._q($id_form)))){
		$index_min = $row['rangmin'];
		$index_max = $row['rangmax'];
	}
	$action_link = generer_action_auteur("forms_update","$id_form",$redirect);

	$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)."ORDER BY rang");
	while ($row = spip_fetch_array($res)) {
		$champ = $row['champ'];
		$visible = ($champ == $champ_visible);
		$nouveau = ($champ == $nouveau_champ);
		$obligatoire = $row['obligatoire'];
		$rang = $row['rang'];
		$aff_min = $rang > $index_min;
		$aff_max = $rang < $index_max;
		$type = $row['type'];

		if ($nouveau) $out .= "<a name='nouveau_champ'></a>";
		else if ($visible) $out .= "<a name='champ_visible'></a>";
		$out .= "<p>\n";
		if (!in_array($type,array('separateur','textestatique')))
			$out .= debut_cadre_relief("", true);
		else
			$out .= debut_cadre_enfonce("", true);
		
		$out .= "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
		if ($aff_min || $aff_max) {
			$out .= "<div class='verdana1' style='float: $spip_lang_right; font-weight: bold;'>";
			$redirect = _DIR_RESTREINT_ABS. generer_url_ecrire('forms_edit',"id_form=$id_form&champ_visible=$champ#champ_visible",false,true);
			if ($retour) $redirect = parametre_url($redirect,'retour',str_replace("&amp;","&",$retour));
			if ($aff_min) {
				$link = generer_action_auteur('forms_champs_deplace',"$id_form-$champ-monter",$redirect);
				$link = parametre_url($link,"time",time()); // pour avoir une url differente de l'actuelle
				$out .= "<a href='$link'><img src='"._DIR_IMG_PACK."monter-16.png' style='border:0' alt='"._T("forms:champ_monter")."'></a>";
			}
			if ($aff_min && $aff_max) {
				$out .= " | ";
			}
			if ($aff_max) {
				$link = generer_action_auteur('forms_champs_deplace',"$id_form-$champ-descendre",$redirect);
				$link = parametre_url($link,"time",time()); // pour avoir une url differente de l'actuelle
				$out .= "<a href='$link'><img src='"._DIR_IMG_PACK."descendre-16.png' style='border:0' alt='"._T("forms:champ_descendre")."'></a>";
			}
			$out .= "</div>\n";
		}

		$out .= $visible ? bouton_block_visible("champ_$champ") : bouton_block_invisible("champ_$champ");
		$out .= "<strong id='titre_nom_$champ'>".typo($row['titre'])."</strong>";
		$out .= "<br /></div>";
		$out .= "(".typo(Forms_nom_type_champ($row['type'])).")\n";
		$out .= $visible ? debut_block_visible("champ_$champ") : debut_block_invisible("champ_$champ");

		// Modifier un champ
		$out .= "<div id='forms_lang_nom_$champ'></div>";
		$out .= "<form class='forms_champ' method='POST' action='$action_link#champ_visible' style='border: 0px; margin: 0px;'>";
		$out .= form_hidden($action_link);
		$out .= "<input type='hidden' name='modif_champ' value='$champ' />";

		$out .= "<div class='verdana2'>";
		$out .= "<p>";
		if ($nouveau) {
			$out .= "<script type='text/javascript'><!-- \nvar antifocus_champ = false; // --></script>\n";
			$js = " onfocus=\"if(!antifocus_champ){this.value='';antifocus_champ=true;}\"";
		}
		else $js = "";
		
		if ($type=='separateur'){
			$out .= "<label for='nom_$champ'>"._T("forms:champ_nom_bloc")."</label> :";
			$out .= " &nbsp;<input type='text' name='nom_champ' id='nom_$champ' value=\"".
				entites_html($row['titre'])."\" class='fondo verdana2' size='30'$js><br />\n";
		}
		else if ($type=='textestatique'){
			$out .= "<label for='nom_$champ'>"._T("forms:champ_nom_texte")."</label> :<br/>";
			$out .= " &nbsp;<textarea name='nom_champ' id='nom_$champ'  class='verdana2' style='width:100%;height:5em;' $js>".
				entites_html($row['titre'])."</textarea><br />\n";
		}
		else{
			$out .= "<label for='nom_$champ'>"._T("forms:champ_nom")."</label> :";
			$out .= " &nbsp;<input type='text' name='nom_champ' id='nom_$champ' value=\"".
				entites_html($row['titre'])."\" class='fondo verdana2' size='30'$js><br />\n";
			$out .= Forms_bloc_edition_champ($row, $action_link);
		}
		$out .= "<label for='aide_$champ'>"._T("forms:aide_contextuelle")."</label> :";
		$out .= " &nbsp;<textarea name='aide_champ' id='aide_$champ'  class='verdana2' style='width:90%;height:3em;' >".
			entites_html($row['aide'])."</textarea><br />\n";
			
		$out .= "<label for='wrap_$champ'>"._T("forms:html_wrapper")."</label> :";
		$out .= " &nbsp;<textarea name='wrap_champ' id='wrap_$champ'  class='verdana2' style='width:90%;height:2em;' >".
			entites_html($row['html_wrap'])."</textarea><br />\n";

		$out .= "<div align='right'>";
		$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo verdana2'></div>\n";
		$out .= "</div>\n";
		$out .= "</form>";
	
		// Supprimer un champ
		$link = parametre_url($action_link,'supp_champ', $champ);
		$out .= "<div style='float: left;'>";
		$out .= icone_horizontale(_T("forms:supprimer_champ"), $link."#champs","../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "supprimer.gif",false);
		$out .= "</div>\n";

		$out .= fin_block();
		if (!in_array($type,array('separateur','textestatique')))
			$out .= fin_cadre_relief(true);
		else
			$out .= fin_cadre_enfonce(true);
	}

	// Ajouter un champ
	$out .= "<p>";
	$out .= debut_cadre_enfonce("", true);
	$out .= "<form method='POST' action='$action_link#nouveau_champ' style='border: 0px; margin: 0px;'>";
	$out .= form_hidden($action_link);
	$out .= "<strong>"._T("forms:ajouter_champ")."</strong><br />\n";
	$out .= _T("forms:ajouter_champ_type");
	$out .= " \n";
	
	$types = Forms_liste_types_champs();
	$out .= "<select name='ajout_champ' value='' class='fondo'>\n";
	foreach ($types as $type) {
		$out .= "<option value='$type'>".typo(Forms_nom_type_champ($type))."</option>\n";
	}
	$out .= "</select>\n";
	$out .= " &nbsp; <input type='submit' name='valider' id='ajout_champ' VALUE='"._T('bouton_ajouter')."' class='fondo'>";
	$out .= "</form>\n";
	$out .= fin_cadre_enfonce(true);
	return $out;
}

function Forms_formulaire_confirme_suppression($id_form,$nb_reponses,$redirect,$retour){
	global $spip_lang_right;
	$out = "";
	if ($nb_reponses){
			$out .= "<p><strong>"._T("forms:attention")."</strong> ";
			$out .= _T("forms:info_supprimer_formulaire_reponses")."</p>\n";
	}
	else{
		$out .= "<p>";
		$out .= _T("forms:info_supprimer_formulaire")."</p>\n";
	}
	$link = generer_action_auteur('forms_supprime',"$id_form",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('forms_tous',"",false,true)));
	$out .= "<form method='POST' action='$link' >";
	$out .= form_hidden($link);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form>\n";

	$out .= "<form method='POST' action='$redirect'>\n";
	$out .= form_hidden($redirect);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form><br />\n";

	return $out;
}

function exec_forms_edit(){
	global $spip_lang_right;
	$retour = _request('retour');

	$id_form = intval(_request('id_form'));
	
	$new = _request('new');
	$supp_form = intval(_request('supp_form'));
	$supp_rejet = _request('supp_rejet');

	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$champconfirm = _request('champconfirm');
	$texte = _request('texte');
	$type_form = _request('type_form');
	$public = _request('public');
	$moderation = _request('moderation');
	
	Forms_install();
	if ($supp_form)
		$id_form = $supp_form;

	if ($retour)
		$retour = urldecode($retour);
	else 
		$retour = generer_url_ecrire('forms_tous');
  include_spip("inc/presentation");
	include_spip("inc/config");

	$nb_reponses = 0;
	if ($id_form)
		if ($row = spip_fetch_array(spip_query("SELECT COUNT(*) AS num FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND confirmation='valide'")))
			$nb_reponses = $row['num'];


	$redirect = generer_url_ecrire('forms_edit');
	if ($retour) 
		$redirect = parametre_url($redirect,"retour",urlencode($retour));
		
	//
	// Affichage de la page
	//

	debut_page("&laquo; $titre &raquo;", "documents", "forms","");
	//
	// Recupere les donnees
	//
	if ($new == 'oui' && !$titre) {
		$titre = _T("forms:nouveau_formulaire");
		include_spip('inc/charset');
		$titre = unicode2charset(html2unicode($titre));
		$descriptif = "";
		$type_form = _request('type_form')?_request('type_form'):""; // possibilite de passer un type par defaut dans l'url de creation
		$email = array();
		$champconfirm = "";
		$texte = "";
		$moderation = "priori";
		$public = "non";
		$js_titre = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		
		$action_link = generer_action_auteur("forms_update","new",$redirect);
	}
	else {
		$champs_visible = _request('champs_visible');
		$nouveau_champ = _request('nouveau_champ');
		$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$type_form = $row['type_form'];
			$email = unserialize($row['email']);
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];
			$moderation = $row['moderation'];
			$public = $row['public'];
		}
		$js_titre = "";
		$action_link = generer_action_auteur("forms_update","$id_form",$redirect);
	}


	debut_gauche();
	echo "<br /><br />\n";
	
	if (Forms_form_administrable($id_form) && $nb_reponses) {
		debut_boite_info();

		$nretour = urlencode(self());
		icone_horizontale(_T("forms:suivi_reponses")."<br />".$nb_reponses." "._T("forms:reponses"),
			generer_url_ecrire('forms_reponses',"id_form=$id_form"), "forum-public-24.gif", "rien.gif");
		icone_horizontale(_T("forms:telecharger_reponses"),
			generer_url_ecrire('forms_telecharger',"id_form=$id_form&retour=$nretour"), "doc-24.gif", "rien.gif");

		if (include_spip('inc/snippets'))
			echo boite_snippets(_T('forms:formulaire'),_DIR_PLUGIN_FORMS."img_pack/form-24.gif",'forms',$id_form);
		fin_boite_info();
	}

	creer_colonne_droite();
	debut_droite();

	if ($supp_form && $supp_rejet==NULL)
		echo Forms_formulaire_confirme_suppression($id_form,$nb_reponses,$redirect,$retour);

	//
	// Cartouche
	//
	if ($id_form) {
		debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png");

		gros_titre($titre);

		if ($descriptif) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('info_descriptif')."</strong> ";
			echo propre($descriptif);
			echo "</div>\n";
		}

		if ($email) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('email_2')."</strong> ";
			echo $email['defaut'];
			echo "</div>\n";
		}
		if ($champconfirm){
			$champconfirm_known = false;
			echo "<div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('forms:confirmer_reponse')."</strong> ";
			$res2 = spip_query("SELECT titre FROM spip_forms_champs WHERE type='email' AND id_form="._q($id_form)." AND champ="._q($champconfirm));
			if ($row2 = spip_fetch_array($res2)){
				echo $row2['nom'] . " ";
				$champconfirm_known = true;
			}
			echo "</div>\n";
			if (($champconfirm_known == true) && ($texte)) {
				echo "<div align='left' border: 1px dashed #aaaaaa;'>";
				echo "<strong class='verdana2'>"._T('info_texte')."</strong> ";
				echo nl2br(entites_html($texte));
				echo "</div>\n";
			}
		}

		if (spip_fetch_array(spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)))) {
			echo "<br />";
			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_invisible("preview_form");
			echo "<strong class='verdana3' style='text-transform: uppercase;'>"
				._T("forms:apparence_formulaire")."</strong>";
			echo "</div>\n";

			echo debut_block_invisible("preview_form");
			echo _T("forms:info_apparence")."<p>\n";
			//echo propre("<form$id_form>");
			include_spip('public/assembler');
			echo inclure_modele('form',$id_form,'','');
			echo fin_block();
		}

		echo afficher_articles(_T("forms:articles_utilisant"),
			array('FROM' => 'spip_articles AS articles, spip_forms_articles AS lien',
			'WHERE' => "lien.id_article=articles.id_article AND id_form="._q($id_form)." AND statut!='poubelle'",
			'ORDER BY' => "titre"));

		fin_cadre_relief();
	}


	//
	// Icones retour et suppression
	//
	echo "<div style='text-align:$spip_lang_right'>";
	if ($retour) {
		icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "rien.gif",'right');
	}
	if ($id_form && Forms_form_administrable($id_form)) {
		echo "<div style='float:$spip_lang_left'>";
		$link = parametre_url(self(),'new','');
		$link = parametre_url($link,'supp_form', $id_form);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('form_tous')));
		}
		icone(_T("forms:supprimer_formulaire"), $link, "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "supprimer.gif");
		echo "</div>";
	}
	echo "<div style='clear:both;'></div>";
	echo "</div>";


	//
	// Edition des donnees du formulaire
	//
	if (Forms_form_editable($id_form)) {
		echo "<p>";
		debut_cadre_formulaire();

		echo "<div class='verdana2'>";
		echo "<form method='POST' action='$action_link' style='border: 0px; margin: 0px;'>";
		echo form_hidden($action_link);

		$titre = entites_html($titre);
		$descriptif = entites_html($descriptif);
		$texte = entites_html($texte);

		echo "<strong><label for='titre_form'>"._T("forms:titre_formulaire")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='titre' id='titre_form' CLASS='formo' ".
			"value=\"".entites_html($titre)."\" size='40'$js_titre><br />\n";

		echo "<strong><label for='desc_form'>"._T('info_descriptif')."</label></strong>";
		echo "<br />";
		echo "<textarea name='descriptif' id='desc_form' class='forml' rows='4' cols='40' wrap='soft'>";
		echo entites_html($descriptif);
		echo "</textarea><br />\n";

		echo Forms_bloc_routage_mail($id_form,$email);

		echo "<strong><label for='confirm_form'>"._T('forms:confirmer_reponse')."</label></strong> ";
		echo "<br />";
		echo "<select name='champconfirm' id='confirm_form' class='forml'>\n";
		echo "<option value=''";
		if ($champconfirm=='') echo " selected='selected'";
		echo ">"._T('forms:pas_mail_confirmation')."</option>\n";
		$champconfirm_known = false;
		$res2 = spip_query("SELECT * FROM spip_forms_champs WHERE type='email' AND id_form="._q($id_form));
		while ($row2 = spip_fetch_array($res2)) {
			echo "<option value='" . $row2['champ'] . "'";
			if ($champconfirm == $row2['champ']){
				echo " selected='selected'";
				$champconfirm_known = true;
			}
			echo ">" . $row2['titre'] . "</option>\n";
		}
		echo "</select><br />\n";
	 	if ($champconfirm_known == true){
			echo "<strong><label for='texte_form'>"._T('info_texte')."</label></strong>";
			echo "<br />";
			echo "<textarea name='texte' id='texte_form' class='formo' rows='4' cols='40' wrap='soft'>";
			echo entites_html($texte);
			echo "</textarea><br />\n";
		}
		else {
			echo "<input type='hidden' name='texte' value=\"" . entites_html($texte);
			echo "\" />\n";
	 	}
	 	
	 	if (in_array($type_form,array('','sondage'))){
			debut_cadre_enfonce("statistiques-24.gif");
			echo "<strong>"._T("forms:type_form")."</strong> : ";
			echo _T("forms:info_sondage");
			echo "<br /><br />";
			afficher_choix('type_form', $type_form, array(
				'' => _T("forms:sondage_non"),
				'sondage' => _T("forms:sondage_oui"),
			));
			fin_cadre_enfonce();
	 	}
	 	else 
	 		echo "<input type='hidden' name='type_form' value='$type_form' />";

		debut_cadre_enfonce("");
		echo "<strong><label for='moderation'>"._T('forms:publication_donnees')."</label></strong>";
	 	echo "<br />";
		echo bouton_radio("public", "oui", _T('forms:donnees_pub'), $public == "oui", "");
		echo "<br />";
		echo bouton_radio("public", "non", _T('forms:donnees_prot'), $public == "non", "");
		echo "<br />";
		fin_cadre_enfonce();
		
		debut_cadre_enfonce("");
		echo "<strong><label for='moderation'>"._T('forms:moderation_donnees')."</label></strong>";
	 	echo "<br />";
		echo bouton_radio("moderation", "posteriori", _T('bouton_radio_publication_immediate'), $moderation == "posteriori", "");
		echo "<br />";
		echo bouton_radio("moderation", "priori", _T('bouton_radio_moderation_priori'), $moderation == "priori", "");
		echo "<br />";
		fin_cadre_enfonce();
		
		echo "<div align='right'>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

		echo "</form>";

		//
		// Creer / modifier des champs
		//
		echo Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ,$redirect);
		
		echo "</div>\n";
		fin_cadre_formulaire();
	}


	fin_page();
}

?>
