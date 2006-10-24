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

function Forms_bloc_edition_champ($row, $link) {
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
			$supp_link = $link;
			$supp_link = parametre_url($supp_link,'supp_choix', $choix);
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

function Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ, $form_link){
	$out = "";
	if (!$id_form) return $out;
	$out .= "<a name='champs'></a>";
	$out .= "<p><hr><p>\n";
	$out .= "<div class='verdana3'>";
	$out .= "<strong>"._T("forms:champs_formuaire")."</strong><br />\n";
	$out .= _T("forms:info_champs_formulaire");
	$out .= "</div>\n";
	$out .= "<div id='forms_lang'></div>";

	if ($row = spip_fetch_array(spip_query("SELECT MAX(rang) AS rangmax, MIN(rang) AS rangmin FROM spip_forms_champs WHERE id_form="._q($id_form)))){
		$index_min = $row['rangmin'];
		$index_max = $row['rangmax'];
	}

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
			$out .= "<div class='verdana1' style='float: right; font-weight: bold;'>";
			if ($aff_min) {
				$link = parametre_url($form_link,'monter', $champ);
				$out .= "<a href='".$link."#champs'><img src='"._DIR_IMG_PACK."monter-16.png' style='border:0' alt='"._T("forms:champ_monter")."'></a>";
			}
			if ($aff_min && $aff_max) {
				$out .= " | ";
			}
			if ($aff_max) {
				$link = parametre_url($form_link,'descendre', $champ);
				$out .= "<a href='".$link."#champs'><img src='"._DIR_IMG_PACK."descendre-16.png' style='border:0' alt='"._T("forms:champ_descendre")."'></a>";
			}
			$out .= "</div>\n";
		}

		$out .= $visible ? bouton_block_visible("champ_$champ") : bouton_block_invisible("champ_$champ");
		$out .= "<strong id='titre_nom_$champ'>".typo($row['titre'])."</strong>";
		$out .= "<br /></div>";
		$out .= "(".Forms_nom_type_champ($row['type']).")\n";
		$out .= $visible ? debut_block_visible("champ_$champ") : debut_block_invisible("champ_$champ");

		// Modifier un champ
		$out .= "<div id='forms_lang_nom_$champ'></div>";
		$out .= "<form class='forms_champ' method='POST' action='"
			. $form_link . "#champ_visible"
			. "' style='border: 0px; margin: 0px;'>";
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
			$out .= Forms_bloc_edition_champ($row, $form_link);
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
		$link = parametre_url($form_link,'supp_champ', $champ);
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
	$out .= "<form method='POST' action='"
		. $form_link. "#nouveau_champ"
		. "' style='border: 0px; margin: 0px;'>";
	$out .= "<strong>"._T("forms:ajouter_champ")."</strong><br />\n";
	$out .= _T("forms:ajouter_champ_type");
	$out .= " \n";
	
	$types = Forms_liste_types_champs();
	$out .= "<select name='ajout_champ' value='' class='fondo'>\n";
	foreach ($types as $type) {
		$out .= "<option value='$type'>".Forms_nom_type_champ($type)."</option>\n";
	}
	$out .= "</select>\n";
	$out .= " &nbsp; <input type='submit' name='valider' id='ajout_champ' VALUE='"._T('bouton_ajouter')."' class='fondo'>";
	$out .= "</form>\n";
	$out .= fin_cadre_enfonce(true);
	return $out;
}

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
function Forms_insere_nouveau_champ($id_form,$type,$titre){
	$champ = Forms_nouveau_champ($id_form,$type);
	$rang = 0;
	$res = spip_query("SELECT max(rang) AS rangmax FROM spip_forms_champs WHERE id_form="._q($id_form));
	if ($row = spip_fetch_array($res))
		$rang = $row['rang'];
	$rang++;
	spip_abstract_insert(
		'spip_forms_champs',
		'(id_form,champ,rang,titre,type,obligatoire,extra_info',
		'('._q($id_form).','._q($champ).','._q($rang).','._q($titre).','.q($type).",'non','')");

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

function Forms_update_edition_champ($id_form,$champ) {
	$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ));
	if ($row = spip_fetch_array($res)){
		$type = $row['type'];
		$extra_info = "";
		if ($type == 'url')
			if ($champ_verif=_request('champ_verif')) $extra_info = $champ_verif;
		if ($type == 'mot') {
			if ($id_groupe = intval(_request('groupe_champ')))
				$extra_info = $id_groupe;
		}
		if ($type == 'fichier') {
			$extra_info = intval(_request('taille_champ'));
		}
		spip_query("UPDATE spip_forms_champs SET extra_info="._q($extra_info)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
		if ($type == 'select' || $type == 'multiple') {
			if (_request('ajout_choix')) {
				$titre = _T("forms:nouveau_choix");
				include_spip('inc/charset');
				$titre = unicode2charset(html2unicode($titre));
				$choix = Forms_insere_nouveau_choix($id_form,$champ,$titre);
			}
			$res2 = spip_query("SELECT choix FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
			while ($row2 = spip_fetch_array($res2)){
				if ($titre = _request($row2['choix']))
					spip_query("UPDATE spip_forms_champs_choix SET titre="._q($titre)." WHERE id_form="._q($id_form)." AND champ="._q($champ)." AND choix="._q($row2['choix']));
			}
		}
	}
}

function Forms_update(){
	$retour = _request('retour');
	$new = _request('new');
	$id_form = intval(_request('id_form'));
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$champconfirm = _request('champconfirm');
	$texte = _request('texte');
	$type_form = _request('type_form');
	$moderation = _request('moderation');
	$public = _request('public');

	$modif_champ = _request('modif_champ');
	$ajout_champ = _request('ajout_champ');
	$nom_champ = _request('nom_champ');
	$champ_obligatoire = _request('champ_obligatoire');
	$aide_champ = _request('aide_champ');
	$wrap_champ = _request('wrap_champ');
	$supp_choix = _request('supp_choix');
	$supp_champ = _request('supp_champ');
	
	$monter = _request('monter');
	$descendre = _request('descendre');

	//
	// Modifications des donnees de base du formulaire
	//
	
	$nouveau_champ = $champ_visible = NULL;
	if (Forms_form_editable($id_form)) {
		// creation
		if ($new == 'oui' && $titre) {
			spip_query("INSERT INTO spip_forms (titre) VALUES ("._q($titre).")");
			$id_form = spip_insert_id();
		}
		// maj
		if ($id_form && $titre) {
			$query = "UPDATE spip_forms SET ".
				"descriptif="._q($descriptif).", ".
				"type_form="._q($type_form).", ".
				"email="._q(serialize($email)).", ".
				"champconfirm="._q($champconfirm).", ".
				"texte="._q($texte).", ".
				"moderation="._q($moderation).", ".
				"public="._q($public)." ".
				"WHERE id_form="._q($id_form);
			$result = spip_query($query);
		}
		// lecture
		$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$type_form = $row['type_form'];
			$moderation = $row['moderation'];
			$public = $row['public'];
			$email = unserialize($row['email']);
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];
		}
	}

	if ($id_form && Forms_form_editable($id_form)) {
		$champ_visible = NULL;
		// Ajout d'un champ
		if (($type = $ajout_champ) && Forms_type_champ_autorise($type)) {
			$titre = _T("forms:nouveau_champ");
			include_spip('inc/charset');
			$titre = unicode2charset(html2unicode($titre));
			$champ = Forms_insere_nouveau_champ($id_form,$type,$titre);
			$champ_visible = $nouveau_champ = $champ;
		}
		// Modif d'un champ
		if ($champ = $modif_champ) {
			if ($row = spip_fetch_array(spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ)))) {
				// switch select to multi ou inversement
				if (_request('switch_select_multi')){
					if ($row['type']=='select') $newtype = 'multiple';
					if ($row['type']=='multiple') $newtype = 'select';
					$newchamp = Forms_nouveau_champ($id_form,$newtype);
					spip_query("UPDATE spip_forms_champs SET type="._q($newtype).", champ="._q($newchamp)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
					spip_query("UPDATE spip_forms_champs_choix SET champ="._q($newchamp)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
					$champ = $newchamp;
				}
				spip_query("UPDATE spip_forms_champs SET titre="._q($nom_champ).", obligatoire="._q($champ_obligatoire).", aide="._q($aide_champ).", html_wrap="._q($wrap_champ)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
				Forms_update_edition_champ($id_form, $champ);
				$champ_visible = $champ;
			}
		}
		// Cas particulier : suppression d'un choix
		if ($choix = $supp_choix){
			if ($row = spip_fetch_array(spip_query("SELECT champ FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND choix="._q($choix)))) {
				spip_query("DELETE FROM spip_forms_champs_choix WHERE choix="._q($choix)." AND id_form="._q($id_form)." AND champ="._q($row['champ']));
				$champ_visible = $row['champ'];
			}
		}
		// Suppression d'un champ
		if ($champ = $supp_champ) {
			spip_query("DELETE FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
			spip_query("DELETE FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ));
		}

		// Monter / descendre un champ
		if (($champ = $monter = _request('monter')) OR ($champ = $descendre = _request('descendre'))) {
			if ($row = spip_fetch_array(spip_query("SELECT rang FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ)))) {
				$rang1 = intval($row['rang']);
				if ($monter) $order = "rang<$rang1 ORDER BY rang DESC";
				else $order = "rang>$rang1 ORDER BY rang";
				if (($row = spip_fetch_array(spip_query("SELECT rang FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND $order LIMIT 0,1")))){
					$rang2 = intval($row['rang']);
					spip_query("UPDATE spip_forms_champs SET rang=$rang1+$rang2-rang WHERE id_form="._q($id_form)." AND rang IN ($rang1,$rang2)");
				}
			}
		}
	}
	return array($id_form,$champ_visible,$nouveau_champ);
}

function Forms_formulaire_confirme_suppression($id_form,$nb_reponses,$form_link,$retour){
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
	$link = generer_action_auteur('forms_supprime',"$id_form",generer_url_ecrire('forms_tous'));
	$out .= "<form method='POST' action='$link' >";
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form>\n";

	$out .= "<form method='POST' action='$form_link'>\n";
	$out .= "<input type='hidden' name='id_form' value='$id_form' />\n";
	$out .= "<input type='hidden' name='retour' value='$retour' />\n";
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

	$clean_link = parametre_url(self(),'new','');
	$form_link = generer_url_ecrire('forms_edit');
	if ($new == 'oui' && !$titre)
		$form_link = parametre_url($form_link,"new",$new);
	if ($retour) 
		$form_link = parametre_url($form_link,"retour",urlencode($retour));

	//
	// Affichage de la page
	//

	debut_page("&laquo; $titre &raquo;", "documents", "forms","");
		
	unset($champ_visible);
	unset($nouveau_champ);
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
	}
	else {
		//
		// Modifications au structure du formulaire
		//
		list($id_form,$champ_visible,$nouveau_champ) = Forms_update();
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
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
	}
	$form_link = parametre_url($form_link,"id_form",$id_form);
	$clean_link = parametre_url($clean_link,"id_form",$id_form);


	debut_gauche();


	echo "<br /><br />\n";
	
	if (Forms_form_administrable($id_form) && $nb_reponses) {
		debut_boite_info();

		$nretour = urlencode(self());
		icone_horizontale(_T("forms:suivi_reponses")."<br />".$nb_reponses." "._T("forms:reponses"),
			generer_url_ecrire('forms_reponses',"id_form=$id_form"), "forum-public-24.gif", "rien.gif");
		icone_horizontale(_T("forms:telecharger_reponses"),
			generer_url_ecrire('forms_telecharger',"id_form=$id_form&retour=$nretour"), "doc-24.gif", "rien.gif");

		fin_boite_info();
	}

	debut_droite();

	if ($supp_form && $supp_rejet==NULL)
		echo Forms_formulaire_confirm_suppression($id_form,$nb_reponses,$form_link,$retour);

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
		$link = parametre_url($clean_link,'supp_form', $id_form);
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
		echo "<form method='POST' action='"
			. $form_link
			. "' style='border: 0px; margin: 0px;'>";

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
		echo Forms_zone_edition_champs($id_form, $champ_visible, $nouveau_champ,$form_link);
		
		echo "</div>\n";
		fin_cadre_formulaire();
	}


	fin_page();
}

?>
