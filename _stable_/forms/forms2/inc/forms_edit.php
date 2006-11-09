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

function Forms_nouveau_champ($id_form,$type){
	$res = spip_query("SELECT champ FROM spip_forms_champs WHERE id_form="._q($id_form)." AND type="._q($type));
	$n = 1;
	$champ = $type.'_'.strval($n);
	while ($row = spip_fetch_array($res)){
		$lenumero = split('_', $row['champ'] );
		$lenumero = intval(end($lenumero));
		if ($lenumero>= $n) $n=$lenumero+1;
	}
	$champ = $type.'_'.strval($n);
	return $champ;
}
function Forms_insere_nouveau_champ($id_form,$type,$titre,$champ=""){
	if (!strlen($champ))
		$champ = Forms_nouveau_champ($id_form,$type);
	$rang = 0;
	$res = spip_query("SELECT max(rang) AS rangmax FROM spip_forms_champs WHERE id_form="._q($id_form));
	if ($row = spip_fetch_array($res))
		$rang = $row['rangmax'];
	$rang++;
	include_spip('base/abstract_sql');
	spip_abstract_insert(
		'spip_forms_champs',
		'(id_form,champ,rang,titre,type,obligatoire,extra_info)',
		'('._q($id_form).','._q($champ).','._q($rang).','._q($titre).','._q($type).",'non','')");
	return $champ;
}
function Forms_nouveau_choix($id_form,$champ){
	$n = 1;
	$res = spip_query("SELECT choix FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
	while ($row = spip_fetch_array($res)){
		$lenumero = split('_', $row['choix']);
		$lenumero = intval(end($lenumero));
		if ($lenumero>= $n) $n=$lenumero+1;
	}
	$choix = $champ.'_'.$n;
	return $choix;
}
function Forms_insere_nouveau_choix($id_form,$champ,$titre){
	$choix = Forms_nouveau_choix($id_form,$champ);
	$rang = 0;
	$res = spip_query("SELECT max(rang) AS rangmax FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
	if ($row = spip_fetch_array($res))
		$rang = $row['rang'];
	$rang++;
	spip_abstract_insert("spip_forms_champs_choix","(id_form,champ,choix,titre,rang)","("._q($id_form).","._q($champ).","._q($choix).","._q($titre).","._q($rang).")");
	return $choix;
}


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

function Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ, $redirect,$ajax=false){
	global $spip_lang_right,$couleur_claire;
	$out = "";
	if (!$id_form) return $out;
	$out .= "<p>";
	$out .= debut_cadre_formulaire('',true);
	$out .= "<div class='verdana3'>";
	$out .= "<strong>"._T("forms:champs_formulaire")."</strong><br />\n";
	$out .= _T("forms:info_champs_formulaire");
	$out .= "</div>\n";
	$out .= "<div id='forms_lang'></div>";

	if ($row = spip_fetch_array(spip_query("SELECT MAX(rang) AS rangmax, MIN(rang) AS rangmin FROM spip_forms_champs WHERE id_form="._q($id_form)))){
		$index_min = $row['rangmin'];
		$index_max = $row['rangmax'];
	}
	$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form).($ajax?" AND champ="._q($ajax):"")." ORDER BY rang");
	while ($row = spip_fetch_array($res)) {
		$champ = $row['champ'];
		$visible = ($champ == $champ_visible)||($champ == $nouveau_champ);
		$nouveau = ($champ == $nouveau_champ);
		$obligatoire = $row['obligatoire'];
		$rang = $row['rang'];
		$aff_min = $rang > $index_min;
		$aff_max = $rang < $index_max;
		$type = $row['type'];

		$redirect = ancre_url(parametre_url($redirect,'champ_visible',$champ),'champ_visible');
		$action_link = generer_action_auteur("forms_edit","$id_form",urlencode($redirect));
		$action_link_noredir = parametre_url($action_link,'redirect','');
		if ($nouveau) $out .= "<a name='nouveau_champ'></a>";
		else if ($visible) $out .= "<a name='champ_visible'></a>";
		$out .= "<p>\n";
		if (!in_array($type,array('separateur','textestatique')))
			$out .= debut_cadre_relief("", true);
		else
			$out .= debut_cadre_enfonce("", true);
		
		$out .= "<div class='verdana1' style='float: $spip_lang_right; font-weight: bold;position:relative;display:inline;'>";
		if ($aff_min) {
			$link = generer_action_auteur('forms_champs_deplace',"$id_form-$champ-monter",urlencode($redirect));
			$link = parametre_url($link,"time",time()); // pour avoir une url differente de l'actuelle
			$out .= "<a href='$link#champs' class='ajaxAction' rel='$redirect'><img src='"._DIR_IMG_PACK."monter-16.png' style='border:0' alt='"._T("forms:champ_monter")."'></a>";
		}
		if ($aff_min && $aff_max) {
			$out .= " | ";
		}
		if ($aff_max) {
			$link = generer_action_auteur('forms_champs_deplace',"$id_form-$champ-descendre",urlencode($redirect));
			$link = parametre_url($link,"time",time()); // pour avoir une url differente de l'actuelle
			$out .= "<a href='$link#champs' class='ajaxAction' rel='$redirect'><img src='"._DIR_IMG_PACK."descendre-16.png' style='border:0' alt='"._T("forms:champ_descendre")."'></a>";
		}
		// Supprimer un champ
		$link = parametre_url($action_link,'supp_champ', $champ);
		$out .= "<a href='$link#champs' class='ajaxAction' rel='$redirect'><img src='"._DIR_IMG_PACK."supprimer.gif' style='border:0' alt='"._T("forms:supprimer_champ")."'></a>";
		$out .= "</div>\n";

		// Modifier un champ
		$formulaire = "";
		$formulaire .= "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
		$formulaire .= $visible ? bouton_block_visible("champ_$champ") : bouton_block_invisible("champ_$champ");
		$formulaire .= "<strong id='titre_nom_$champ'>".typo($row['titre'])."</strong>";
		$formulaire .= "<br /></div>";
		$formulaire .= "(".typo(Forms_nom_type_champ($row['type'])).")\n";
		$formulaire .= $visible ? debut_block_visible("champ_$champ") : debut_block_invisible("champ_$champ");

		$formulaire .= "<div id='forms_lang_nom_$champ'></div>";
		
		$formulaire .= "<form class='ajaxAction' method='POST' action='$action_link_noredir'" .
			" style='border: 0px; margin: 0px;'>" .
			form_hidden($action_link_noredir) .
			"<input type='hidden' name='redirect' value='$redirect' />" . // form_hidden ne desencode par redirect ...
			"<input type='hidden' name='idtarget' value='champs-$id_form-$champ' />" .
			"<input type='hidden' name='modif_champ' value='$champ' />";

		$formulaire .= "<div class='verdana2'>";
		if ($nouveau) {
			$formulaire .= "<script type='text/javascript'><!-- \nvar antifocus_champ = false; // --></script>\n";
			$js = " onfocus=\"if(!antifocus_champ){this.value='';antifocus_champ=true;}\"";
		}
		else $js = "";
		
		if ($type=='separateur'){
			$formulaire .= "<label for='nom_$champ'>"._T("forms:champ_nom_bloc")."</label>&nbsp;:";
			$formulaire .= " &nbsp;<input type='text' name='nom_champ' id='nom_$champ' value=\"".
				entites_html($row['titre'])."\" class='fondo verdana2' size='30'$js /><br />\n";
		}
		else if ($type=='textestatique'){
			$formulaire .= "<label for='nom_$champ'>"._T("forms:champ_nom_texte")."</label>&nbsp;:<br/>";
			$formulaire .= " &nbsp;<textarea name='nom_champ' id='nom_$champ'  class='verdana2' style='width:100%;height:5em;' $js>".
				entites_html($row['titre'])."</textarea><br />\n";
		}
		else{
			$formulaire .= "<label for='nom_$champ'>"._T("forms:champ_nom")."</label> :";
			$formulaire .= " &nbsp;<input type='text' name='nom_champ' id='nom_$champ' value=\"".
				entites_html($row['titre'])."\" class='fondo verdana2' size='30'$js /><br />\n";
			$formulaire .= Forms_bloc_edition_champ($row, $action_link);
		}
		$formulaire .= "<label for='aide_$champ'>"._T("forms:aide_contextuelle")."</label> :";
		$formulaire .= " &nbsp;<textarea name='aide_champ' id='aide_$champ'  class='verdana2' style='width:90%;height:3em;' >".
			entites_html($row['aide'])."</textarea><br />\n";
			
		$formulaire .= "<label for='wrap_$champ'>"._T("forms:html_wrapper")."</label> :";
		$formulaire .= " &nbsp;<textarea name='wrap_champ' id='wrap_$champ'  class='verdana2' style='width:90%;height:2em;' >".
			entites_html($row['html_wrap'])."</textarea><br />\n";

		$formulaire .= "<div style='text-align:$spip_lang_right'>";
		$formulaire .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo verdana2'>\n";
		$formulaire .= "</div>\n";

		// Supprimer un champ
		/*$link = parametre_url($action_link,'supp_champ', $champ);
		$formulaire .= icone_horizontale('',"<a href='$link#champs' class='ajaxAction' rel='$redirect'>"._T("forms:supprimer_champ")."</a>","../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "supprimer.gif",false);*/

		$args_redir=parametre_url($redirect,'exec','','&');
		$args_redir=explode("#",$args_redir);
		$args_redir=explode("?",$args_redir[0]);
		$args_redir="&".$args_redir[1];

		$formulaire .= "</div>";
		$formulaire .= "</form>";
		$formulaire .= fin_block();
		//$formulaire = ajax_action_auteur('forms_edit', "$id_form-$champ","forms_edit","$args_redir#forms_edit-$id_form-$champ", $formulaire, "$args_redir&bloc=champs&ajax_champ=$champ#champ_visible",'');
		/*$formulaire = "<form class='ajaxAction' method='POST' action='$action_link_noredir'" .
			" style='border: 0px; margin: 0px;'>" .
			form_hidden($action_link_noredir) .
			"<input type='hidden' name='redirect' value='$redirect' />" . // form_hidden ne desencode par redirect ...
			$formulaire .
			"</form>";*/
		
		if ($ajax && ($champ == $ajax))
			return $formulaire;
		//$out .= "<div id='forms_edit-$id_form-$champ' class='forms_champs'>$formulaire</div>";
		$out .= "<div id='champs-$id_form-$champ' class='forms_champs'>$formulaire</div>";
		if (!in_array($type,array('separateur','textestatique')))
			$out .= fin_cadre_relief(true);
		else
			$out .= fin_cadre_enfonce(true);
	}

	// Ajouter un champ
	$redirect = ancre_url(parametre_url($redirect,'champ_visible',''),'');
	$action_link = generer_action_auteur("forms_edit","$id_form",urlencode($redirect));
	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<p>";
	$out .= debut_cadre_enfonce("", true);
	$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir' style='border: 0px; margin: 0px;'>" .
		form_hidden($action_link_noredir) .
		"<input type='hidden' name='redirect' value='$redirect' />" . // form_hidden ne desencode par redirect ...
		"<input type='hidden' name='idtarget' value='champs' />"; // on target toute la boite, pas juste le div parent
	$out .=	"<strong>"._T("forms:ajouter_champ")."</strong><br />\n";
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
	$out .= "</p>";
	$out .= fin_cadre_formulaire(true);
	$out .= "</p>";
	
	return $out;
}


//
// Edition des donnees du formulaire
//
function boite_proprietes($id_form, $row, $js_titre, $action_link) {
	$out = "";
	$out .= "<p>";
	$out .= debut_cadre_formulaire('',true);

	$out .= "<div class='verdana2'>";
	$out .= "<form method='POST' action='$action_link' style='border: 0px; margin: 0px;'>";
	$out .= form_hidden($action_link);

	$titre = entites_html($row['titre']);
	$descriptif = entites_html($row['descriptif']);
	$texte = entites_html($row['texte']);
	$email = unserialize($row['email']);

	$out .= "<strong><label for='titre_form'>"._T("forms:titre_formulaire")."</label></strong> "._T('info_obligatoire_02');
	$out .= "<br />";
	$out .= "<input type='text' name='titre' id='titre_form' CLASS='formo' ".
		"value=\"".entites_html($titre)."\" size='40'$js_titre><br />\n";

	$out .= "<strong><label for='desc_form'>"._T('info_descriptif')."</label></strong>";
	$out .= "<br />";
	$out .= "<textarea name='descriptif' id='desc_form' class='forml' rows='4' cols='40' wrap='soft'>";
	$out .= entites_html($descriptif);
	$out .= "</textarea><br />\n";

	$out .= Forms_bloc_routage_mail($id_form,$email);

	$out .= "<strong><label for='confirm_form'>"._T('forms:confirmer_reponse')."</label></strong> ";
	$out .= "<br />";
	$out .= "<select name='champconfirm' id='confirm_form' class='forml'>\n";
	$out .= "<option value=''>"._T('forms:pas_mail_confirmation')."</option>\n";
	$champconfirm_known = false;
	$res2 = spip_query("SELECT * FROM spip_forms_champs WHERE type='email' AND id_form="._q($id_form));
	while ($row2 = spip_fetch_array($res2)) {
		$out .= "<option value='" . $row2['champ'] . "'";
		if ($row['champconfirm'] == $row2['champ']){
			$out .= " selected='selected'";
			$champconfirm_known = true;
		}
		$out .= ">" . $row2['titre'] . "</option>\n";
	}
	$out .= "</select><br />\n";
 	if ($champconfirm_known == true){
		$out .= "<strong><label for='texte_form'>"._T('info_texte')."</label></strong>";
		$out .= "<br />";
		$out .= "<textarea name='texte' id='texte_form' class='formo' rows='4' cols='40' wrap='soft'>";
		$out .= $texte;
		$out .= "</textarea><br />\n";
	}
	else {
		$out .= "<input type='hidden' name='texte' value=\"$texte\" />\n";
 	}
 	
 	if (in_array($row['type_form'],array('','sondage'))){
		$out .= debut_cadre_enfonce("statistiques-24.gif",true);
		$out .= "<strong>"._T("forms:type_form")."</strong> : ";
		$out .= _T("forms:info_sondage");
		$out .= "<br /><br />";
		$out .= bouton_radio('type_form', '', _T("forms:sondage_non"), $type_form == '').'<br />';
		$out .= bouton_radio('type_form', 'sondage', _T("forms:sondage_oui"), $type_form == 'sondage').'<br />';
		$out .= fin_cadre_enfonce(true);
 	}
 	else 
 		$out .= "<input type='hidden' name='type_form' value='$type_form' />";

	$out .= debut_cadre_enfonce("",true);
	$out .= "<strong><label for='moderation'>"._T('forms:publication_donnees')."</label></strong>";
 	$out .= "<br />";
	$out .= bouton_radio("public", "oui", _T('forms:donnees_pub'), $public == "oui", "");
	$out .= "<br />";
	$out .= bouton_radio("public", "non", _T('forms:donnees_prot'), $public == "non", "");
	$out .= "<br />";
	$out .= fin_cadre_enfonce(true);
	
	$out .= debut_cadre_enfonce("",true);
	$out .= "<strong><label for='moderation'>"._T('forms:moderation_donnees')."</label></strong>";
 	$out .= "<br />";
	$out .= bouton_radio("moderation", "posteriori", _T('bouton_radio_publication_immediate'), $moderation == "posteriori", "");
	$out .= "<br />";
	$out .= bouton_radio("moderation", "priori", _T('bouton_radio_moderation_priori'), $moderation == "priori", "");
	$out .= "<br />";
	$out .= fin_cadre_enfonce(true);
	
	$out .= "<div align='right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

	$out .= "</form>";
	$out .= "</div>";
	$out .= fin_cadre_formulaire(true);
	$out .= "</p>";
	return $out;
}
?>