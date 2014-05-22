<?php

	


function inc_iconifier($id_objet, $id,  $script, $visible=false, $flag_modif=true) {
	include_spip("inc/iconifier");


	if ($GLOBALS['spip_display'] == 4) return "";
	$texteon = $GLOBALS['logo_libelles'][($id OR $id_objet != 'id_rubrique') ? $id_objet : 'id_racine'];

	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	
	// Add the redirect url when uploading via iframe
	$iframe_script = generer_url_ecrire('iconifier',"type=$id_objet&$id_objet=$id&script=$script",true);
	$iframe = "<input type='hidden' name='iframe_redirect' value='".rawurlencode($iframe_script)."' />\n";

	$logo = $chercher_logo($id, $id_objet, 'on');
	$logo_s = $chercher_logo($id, $id_objet, 'off');
	if (!$logo) {
		if ($flag_modif AND $GLOBALS['meta']['activer_logos'] != 'non') {
			$masque = indiquer_logo($texteon, $id_objet, 'on', $id, $script, $iframe);
			$masque = "<div class='cadre_padding'>$masque</div>";
			$bouton = bouton_block_depliable($texteon, $visible, "on-$id_objet-$id");
			$res = debut_block_depliable($visible,"on-$id_objet-$id") . $masque . fin_block();
		}
	} else {
	
	
		list($img, $clic) = decrire_logo($id_objet,'on',$id, 170, 170, $logo, $texteon, $script, $flag_modif AND !$logo_s);

		$bouton = bouton_block_depliable($texteon, $visible, "on-$id_objet-$id");


		$survol = '';
		$texteoff = _T('logo_survol');
		if (!$logo = $logo_s) {
			if ($flag_modif AND $GLOBALS['meta']['activer_logos_survol'] == 'oui') {
				$masque = "<br />".indiquer_logo($texteoff, $id_objet, 'off', $id, $script, $iframe);
				$survol .= "<br />".block_parfois_visible("off-$id_objet-$id", $texteoff, $masque, null, $visible);
			}
			$masque = debut_block_depliable($visible,"on-$id_objet-$id") 
				. "<div class='cadre_padding'>"
				. $clic . $survol . "</div>" . fin_block();
		} else {
			list($imgoff, $clicoff) = decrire_logo($id_objet, 'off', $id, 170, 170, $logo, $texteoff, $script, $flag_modif);			
			$masque = debut_block_depliable($visible, "off-$id_objet-$id") .  $clicoff . fin_block();
			$survol .= "<br />".bouton_block_depliable($texteoff, $visible, "off-$id_objet-$id")
			. "<div class='cadre_padding'>".$imgoff.$masque."</div>";
			$masque = debut_block_depliable($visible,"on-$id_objet-$id") . $clic . fin_block() . $survol;
		}
		
		if ($img)  {
			if ($script == "articles" OR ($script == "naviguer" AND $id > 0) OR $script == "mots_edit" OR $script == "sites" OR $script == "auteur_infos" OR $script == "breves_voir") {
				
				$flag_modif = false;
				
				if ($script == "articles") {
					$query = sql_query("SELECT titre_logo, descriptif_logo FROM spip_articles WHERE id_article=$id");
					$flag_modif =  autoriser('modifier', 'article', $id);
				} else if ($script == "naviguer") {
					$query = sql_query("SELECT titre_logo, descriptif_logo FROM spip_rubriques WHERE id_rubrique=$id");
					$flag_modif =  autoriser('modifier', 'rubrique', $id);
				} else if ($script == "mots_edit") {
					$query = sql_query("SELECT titre_logo, descriptif_logo FROM spip_mots WHERE id_mot=$id");
					$flag_modif =  autoriser('modifier', 'mot', $id);
				} else if ($script == "sites") {
					$query = sql_query("SELECT titre_logo, descriptif_logo FROM spip_syndic WHERE id_syndic=$id");
					$flag_modif =  autoriser('modifier', 'site', $id);
				} else if ($script == "auteur_infos") {
					$query = sql_query("SELECT titre_logo, descriptif_logo FROM spip_auteurs WHERE id_auteur=$id");
					$flag_modif =  autoriser('modifier', 'auteur', $id);
				} else if ($script == "breves_voir") {
					$query = sql_query("SELECT titre_logo, descriptif_logo FROM spip_breves WHERE id_breve=$id");
					$flag_modif =  autoriser('modifier', 'breve', $id);
				}


				if ($row = sql_fetch($query)) {
					$titre_logo = $row["titre_logo"];
					$descriptif_logo = $row["descriptif_logo"];
					$aff_titre = typo($titre_logo); 
				}
			
				if (!$aff_titre) {
					$aff_titre = extraire_attribut($img, "src");
					$aff_titre = substr($aff_titre, strrpos($aff_titre, "/")+1, strlen($aff_titre));
				}
				
				
				$form = "<div style='font-size: 10px; text-align: left; padding: 5px;'>";
				
				if ($flag_modif) {
				$form .= bouton_block_depliable($aff_titre, false, "formulaire_logo");
				if ($descriptif_logo) $form .= propre($descriptif_logo);
								
				$form .= debut_block_depliable(false, "formulaire_logo");
					
				$form .= "<form method='post' action='?exec=$script&$id_objet=$id'><div class='formulaire_spip formulaire_spip_compact'>
				<ul>
				<input type='hidden' name='modifier_titre_logo' value='oui' />
				<input type='hidden' name='id' value='$id' />
				<li><label>"._T('info_titre')."</label><input name='titre_logo' type='text' value=\"".htmlspecialchars($titre_logo)."\" class='text' onfocus=\"$('#validation_logo').slideDown();\" /></li>
				<li><label>"._T('info_descriptif')."</label><textarea class='textarea' name='descriptif_logo' rows='4' onfocus=\"$('#validation_logo').slideDown();\">".htmlspecialchars($descriptif_logo)."</textarea></li>
				</ul>
				<div id='validation_logo' style='display: none;' class='boutons'><input type='submit' /></div>
				</div></form>";
				$form .= fin_block();
				} else {
					if ($titre_logo) $form .= "<div style='font-weight: bold;'>".propre($titre_logo)."</div>";
					if ($descriptif_logo) $form .= propre($descriptif_logo);
				}
				
				
				$form .= "</div>";
			}
		}

		$res = "$img$masque$form";
	}

	if ($res) {
		$res = debut_cadre('r', 'image-24.gif', '', $bouton, '', '', false)
			. $res
			. fin_cadre_relief(true);

		if(_request("exec")!="iconifier") {
		  $js .= http_script('',  'async_upload.js')
		    . http_script('$("form.form_upload_icon").async_upload(async_upload_icon)');

		} else $js = "";
		return ajax_action_greffe("iconifier", $id, $res).$js;
	}
	else return '';
}


if (_request("modifier_titre_logo")) {
	$connect_id = $auteur_session["id_auteur"];
	$script = _request("exec");
	$id = floor(_request("id"));
	$titre_logo = _request("titre_logo");
	$descriptif_logo = _request("descriptif_logo");
	
	include_spip("inc/autoriser");
	
	if ($script == "articles" AND autoriser('modifier', 'article', $id)) {
		include_spip("base/abstract_sql");
		sql_updateq("spip_articles", 
			array("titre_logo" => $titre_logo, "descriptif_logo" => $descriptif_logo), 
			"id_article = '$id'");

	} else if ($script == "naviguer" AND autoriser('modifier', 'rubrique', $id)) {
		include_spip("base/abstract_sql");
		sql_updateq("spip_rubriques", 
			array("titre_logo" => $titre_logo, "descriptif_logo" => $descriptif_logo), 
			"id_rubrique = '$id'");
			
	} else if ($script == "mots_edit" AND autoriser('modifier', 'mot', $id)) {
		include_spip("base/abstract_sql");
		sql_updateq("spip_mots", 
			array("titre_logo" => $titre_logo, "descriptif_logo" => $descriptif_logo), 
			"id_mot = '$id'");
	} else if ($script == "sites" AND autoriser('modifier', 'site', $id)) {
		include_spip("base/abstract_sql");
		sql_updateq("spip_syndic", 
			array("titre_logo" => $titre_logo, "descriptif_logo" => $descriptif_logo), 
			"id_syndic = '$id'");
	} else if ($script == "auteur_infos" AND autoriser('modifier', 'auteur', $id)) {
		include_spip("base/abstract_sql");
		sql_updateq("spip_auteurs", 
			array("titre_logo" => $titre_logo, "descriptif_logo" => $descriptif_logo), 
			"id_auteur = '$id'");
	} else if ($script == "breves_voir" AND autoriser('modifier', 'breve', $id)) {
		include_spip("base/abstract_sql");
		sql_updateq("spip_breves", 
			array("titre_logo" => $titre_logo, "descriptif_logo" => $descriptif_logo), 
			"id_breve = '$id'");
	}

}

?>