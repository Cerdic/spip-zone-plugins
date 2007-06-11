<?php

include_spip('inc/plugin');

function noisetier_gestion_zone ($zone, $page, $cadre_enfonce=false, $ajax=false) {
	global $theme_zones;
	$out = '';
	if (isset($theme_zones[$zone]['insere_avant:'.$page]))
		$out .= $theme_zones[$zone]['insere_avant:'.$page];
	else
		$out .= $theme_zones[$zone]['insere_avant'];

	$out .= "<div id='zone-$zone' name='zone-$zone'>";
	if (isset($theme_zones[$zone]['titre']))
		$titre_zone = typo($theme_zones[$zone]['titre'])." <span style='font-size:85%;font-weight:normal;'>($zone)</span>";
	else
		$titre_zone = "$zone";
	if ($cadre_enfonce)
		if ($zone=='head') $out .= debut_cadre_enfonce("../"._DIR_PLUGIN_NOISETIER."/img_pack/zone-24.png",true,'',$titre_zone);
		else $out .= debut_cadre_enfonce("../"._DIR_PLUGIN_NOISETIER."/img_pack/zone-warning-24.png",true,'',$titre_zone);
	else $out .= debut_cadre_trait_couleur("../"._DIR_PLUGIN_NOISETIER."/img_pack/zone-24.png",true,'',$titre_zone);

	if (isset($theme_zones[$zone]['descriptif'])){
			if (!$cadre_formulaire) $style_descriptif = 'font-size:90%;'; else $style_descriptif = '';
			$out .= "<div style='$style_descriptif'>".typo($theme_zones[$zone]['descriptif'])."</div>";
		}
		$out .= "<br />";

		$contenu_zone = '';
		$contenu_zone .= "<div id='contenuzone-$zone'>";
		//Afficher les différentes noisettes 
		if ($page=='') $condition = "";
		else $condition = " AND page REGEXP '(^toutes$)|((^|,)$page(,|$))' AND exclue NOT REGEXP '((^|,)$page(,|$))'";
		$query = "SELECT * FROM spip_noisettes WHERE zone='$zone'$condition ORDER BY position";
		$res = spip_query($query);
		while ($row = spip_fetch_array($res)) {
			$type = $row['type'];
			$id_noisette = $row['id_noisette'];
			if ($type=='texte') $contenu_zone .= noisetier_affiche_texte($id_noisette, $page, $row);
			if ($type=='noisette') $contenu_zone .= noisetier_affiche_noisette($id_noisette, $page, $row);
		}
		
		$contenu_zone .= "</div>";
		
		if ($ajax) return $contenu_zone;
		
		$out .= $contenu_zone;
		
		//Formulaire d'ajout d'une noisette
		if ((!$cadre_enfonce OR $zone=='head') AND autoriser('gerer','noisetier'))
			$out .= noisetier_form_ajout_noisette_texte($page==''?'toutes':$page,$zone);

	if ($cadre_enfonce) $out .= fin_cadre_enfonce(true); else $out .= fin_cadre_trait_couleur(true);
	$out .= '<br />';

	$out .= "</div>";
	if (isset($theme_zones[$zone]['insere_apres:'.$page]))
		$out .= $theme_zones[$zone]['insere_apres:'.$page];
	else
		$out .= $theme_zones[$zone]['insere_apres'];
	
	return $out;
}

// Liste les éléments html d'un sous répertoire qu'ils soient dans un plugin ou dans le répertoire squelettes
function noisetier_liste_elements_sousrepertoire ($sousrepertoire) {
	static $liste;
	if (!isset($liste)) {
		$liste = array();
		// Recherche dans le répertoire des squelettes
		$liste = array_merge($liste,preg_files("../squelettes".$plugin."/$sousrepertoire/"));
		// Recherche dans les plugins
		$lcpa = liste_chemin_plugin_actifs();
		foreach ($lcpa as $plugin)
			$liste = array_merge($liste,preg_files(_DIR_PLUGINS.$plugin."/$sousrepertoire/"));
	}
	return $liste;
}

// Liste des noisettes possibles
function noisetier_liste_noisettes() {
	static $result;
	if (!isset($result)) {
		$result = array();
		$liste = noisetier_liste_elements_sousrepertoire ('noisettes');
		foreach ($liste as $chemin) {
			if(preg_match('/\/noisettes\/([[:graph:]]+).htm/',$chemin,$noisette)) $result[$noisette[1]]=$chemin;
		}
		asort($result);
	}
	return $result;
}

//Calculer la liste des positions au sein d'une zone en fonction de la page en cours.
function noisetier_liste_positions($page,$zone) {
	static $liste;
	if(!isset($liste)) {
		$liste = array();
		if ($page=='') $condition = "";
		else $condition = " AND page REGEXP '(^toutes$)|((^|,)$page(,|$))' AND exclue NOT REGEXP '((^|,)$page(,|$))'";
		$query = "SELECT position FROM spip_noisettes WHERE zone='$zone'$condition ORDER BY position";
		$res = spip_query($query);
		while ($row = spip_fetch_array($res)) {
			$liste[] = $row['position'];
		}
	}
}

// Formulaire d'ajout d'une noisette ou d'un texte
function noisetier_form_ajout_noisette_texte($page,$zone) {
	$out = '';
	$out .= debut_cadre_formulaire('', true);
	$out .= bouton_block_depliable(_T('noisetier:ajout_noisette_texte'),false,"form-ajout-$zone");
	$redirect = generer_url_ecrire('noisetier',($page=='')?'':"page=$page");
	$out .= debut_block_depliable(false,"form-ajout-$zone");
	//Ajout d'une noisette
	$action_link = generer_action_auteur("noisetier_ajout", 'ajout_noisette', $redirect);
	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<form class='ajaxAction' name='ajout_noisette_$zone' method='POST' action='$action_link_noredir' style='border: 0px; margin: 10px 0px; border-bottom: 1px dashed #999;''>";
	$out .= form_hidden($action_link_noredir);
	$out .= "<input type='hidden' name='redirect' value='$redirect' />";
	$out .= "<input type='hidden' name='idtarget' value='zone-$zone' />";
	$out .= "<input type='hidden' name='page' value='$page' />";
	$out .= "<input type='hidden' name='zone' value='$zone' />";
	$out .= _T('noisetier:ajout_selection_noisette');
	$out .= " <select name='url_noisette' value='' class='fondo' style='width:150px;'>\n";
	$liste_noisettes = noisetier_liste_noisettes();
	foreach ($liste_noisettes as $nom => $chemin) 
		$out .= "<option value='$chemin'>$nom</option>\n";
	$out .= "</select>";
	$out .= icone_horizontale(_T('noisetier:ajout_noisette'), "javascript: document.forms.ajout_noisette_$zone.submit();", "../"._DIR_PLUGIN_NOISETIER."/img_pack/noisette-24.png", "creer.gif",false);
	$out .= "</form>";
	//Ajout d'un texte
	$action_link = generer_action_auteur("noisetier_ajout", 'ajout_texte', $redirect);
	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<form class='ajaxAction' name='ajout_texte_$zone' method='POST' action='$action_link_noredir' style='border: 0px; margin: 10px 0px; >";
	$out .= form_hidden($action_link_noredir);
	$out .= "<input type='hidden' name='redirect' value='$redirect' />";
	$out .= "<input type='hidden' name='idtarget' value='zone-$zone' />";
	$out .= "<input type='hidden' name='page' value='$page' />";
	$out .= "<input type='hidden' name='zone' value='$zone' />";
	$out .= icone_horizontale(_T('noisetier:ajout_texte'), "javascript: document.forms.ajout_texte_$zone.submit();", "../"._DIR_PLUGIN_NOISETIER."/img_pack/texte-24.png", "creer.gif",false);
	$out .= "</form>";

	$out .= fin_block();
	$out .= fin_cadre_formulaire(true);

	return $out;
}

// Affiche un texte
function noisetier_affiche_texte($id_noisette, $page, $row=NULL) {
	global $couleur_claire, $spip_lang_left, $spip_lang_right;
	$noisette_visible = _request('noisette_visible');
	if ($row==NULL)
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_noisettes WHERE type='texte' AND id_noisette=$id_noisette"));
	if (!$row) return '';
	$redirect = generer_url_ecrire('noisetier',($page=='')?'':"page=$page");
	$zone = $row['zone'];
	
	$out = '';
	$out .= "<div id='noisette-$id_noisette'>";
	$out .= debut_cadre_relief('', true);
	$out .= "<a name='noisette-$id_noisette'></a>";
	$out .= "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/texte-24.png' class ='sortableChampsHandle' style='float:$spip_lang_left;position:relative;margin-right:5px;'/>";
	
	$out .= "<div class='verdana1' style='float: $spip_lang_right; font-weight: bold;position:relative;display:inline;'>";
	//Fleches monter/descendre
	$out .= "<span class='boutons_ordonne'>";
		$position = $row['position'];
		$liste_positions = noisetier_liste_positions($page,$zone);
		$aff_min=true;
		$aff_max=true;
		if ($aff_min) {
			$link = generer_action_auteur('forms_champs_deplace',"$id_form-$champ-monter",urlencode($redirect));
			$link = parametre_url($link,"time",time()); // pour avoir une url differente de l'actuelle
			$out .= "<a href='$link#champs' class='ajaxAction' rel='$redirect'><img src='"._DIR_IMG_PACK."monter-16.png' style='border:0' alt='"._T("noisetier:monter")."'></a>";
		}
		if ($aff_max) {
			$link = generer_action_auteur('forms_champs_deplace',"$id_form-$champ-descendre",urlencode($redirect));
			$link = parametre_url($link,"time",time()); // pour avoir une url differente de l'actuelle
			$out .= "<a href='$link#champs' class='ajaxAction' rel='$redirect'><img src='"._DIR_IMG_PACK."descendre-16.png' style='border:0' alt='"._T("noisetier:descendre")."'></a>";
		}
		$out .= "</span>";
	// Actif ?
	$actif = $row['actif'];
	if ($actif=='oui') $puce = "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/actif-on-16.png' style='border:0' alt='"._T("noisetier:noisette_active")."'>";
	else $puce = "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/actif-off-16.png' style='border:0' alt='"._T("noisetier:noisette_non_active")."'>";
	if (autoriser('gerer','noisetier')){
		if($actif=='oui') $action_link = generer_action_auteur("noisetier_active","desactiver-$id_noisette",$redirect);
		else $action_link = generer_action_auteur("noisetier_active","activer-$id_noisette",$redirect);
		$action_link_noredir = parametre_url($action_link,'redirect','');
		$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir' style='border:0; margin:0; display:inline;' name='form_active_$id_noisette'>";
		$out .= form_hidden($action_link_noredir);
		$out .= "<input type='hidden' name='redirect' value='$redirect' />";
		$out .= "<input type='hidden' name='idtarget' value='noisette-$id_noisette' />";
		if ($actif=='oui') $title = _T('noisetier:desactiver');
		else $title = _T('noisetier:activer');
		$out .= "<a href='javascript: document.forms.form_active_$id_noisette.submit();' title='$title'>";
		$out .= $puce;
		$out .= "</a></form>";
	}
	else $out .= $puce;
	$out .= "</div>\n";
	
	// Titre du texte
	$out .= "<div style='padding: 0px 20px 2px 30px; color: black;'>";
	if ($noisette_visible==$id_noisette)
		$out .= bouton_block_depliable(typo($row['titre']),true,"block-noisette-$id_noisette");
	else
		$out .= bouton_block_depliable(typo($row['titre']),false,"block-noisette-$id_noisette");
	$out .= "<div style='font-size:90%;'>".couper(typo($row['descriptif']),150)."</div></div>";
	if ($noisette_visible==$id_noisette)
		$out .= debut_block_depliable(true,"block-noisette-$id_noisette");
	else
		$out .= debut_block_depliable(false,"block-noisette-$id_noisette");
	
	//Modification du texte
	$redirect = ancre_url($redirect,"noisette-$id_noisette");
	if($row['descriptif']!='')
		$out .= "<div style='margin:5px 0; padding:5px 0; border-top:1px dashed #999;'>".propre($row['descriptif'])."</div>";
	$out .= "<div style='margin:5px 0; padding:5px 0; border-top:1px dashed #999;' class='serif'>";
	$out .= "<b class='serif'>id_noisette&nbsp;:</b> $id_noisette<p />";
	$out .= debut_cadre_formulaire('',true);
	$action_link = generer_action_auteur("noisetier_editer","texte-$id_noisette",$redirect);
	$action_link_noredir = parametre_url($action_link,'redirect','');
	$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir' style='border: 0; margin:0;' name='editer_$id_noisette'>";
	$out .= form_hidden($action_link_noredir);
	$out .= "<input type='hidden' name='redirect' value='$redirect' />";
	$out .= "<input type='hidden' name='idtarget' value='noisette-$id_noisette' />";
	$titre = entites_html($row['titre']);
	$descriptif = entites_html($row['descriptif']);
	$out .= "<b>"._T('noisetier:info_titre_texte')."</b> ["._T('noisetier:info_non_insere')."]</br>";
	$out .= "<input type='text' name='titre' class='formo' value=\"$titre\" size='40'/>";
	$out .= "<p /><b>"._T('noisetier:info_texte')."</b></br>";
	$out .= "<textarea name='descriptif' class='forml' rows='4' cols='40'>";
	$out .= $descriptif;
	$out .= "</textarea>";
	$out .= "<div style='text-align: right'><input type='submit' value='"._T('bouton_enregistrer')."' class='fondo' /></div>";
	$out .= "</form>";
	$out .= fin_cadre_formulaire(true);
	$out .= "</div>";
	
	//Supression du texte (faire un formulaire)
	if (autoriser('gerer','noisetier')) {
		$redirect = ancre_url($redirect,"zone-$zone");
		$action_link = generer_action_auteur("noisetier_suppression","suppression-$id_noisette",$redirect);
		$action_link_noredir = parametre_url($action_link,'redirect','');
		$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir' style='border:0; margin:0; display:inline;' name='sup_texte_$id_noisette'>";
		$out .= form_hidden($action_link_noredir);
		$out .= "<input type='hidden' name='redirect' value='$redirect' />";
		$out .= "<input type='hidden' name='idtarget' value='zone-$zone' />";
		$out .= icone_horizontale(_T('noisetier:supprimer_texte'), "javascript: document.forms.sup_texte_$id_noisette.submit();", "../"._DIR_PLUGIN_NOISETIER."/img_pack/texte-24.png", "supprimer.gif",false);
		$out .= "</form>";
	}
	$out .= fin_block();
	$out .= fin_cadre_relief(true);
	$out .= "</div>";
	return $out;
}

// Affiche une noisette
function noisetier_affiche_noisette($id_noisette, $page, $row=NULL) {
	global $couleur_claire, $spip_lang_left, $spip_lang_right;
	$noisette_visible = _request('noisette_visible');
	if ($row==NULL)
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_noisettes WHERE type='noisette' AND id_noisette=$id_noisette"));
	if (!$row) return '';
	$redirect = generer_url_ecrire('noisetier',($page=='')?'':"page=$page");
	$zone = $row['zone'];
	
	$out = '';
	$out .= "<div id='noisette-$id_noisette'>";
	$out .= debut_cadre_relief('', true);
	$out .= "<a name='noisette-$id_noisette'></a>";
	$out .= "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/noisette-24.png' class ='sortableChampsHandle' style='float:$spip_lang_left;position:relative;margin-right:5px;'/>";
	// Actif ?
	$actif = $row['actif'];
	if ($actif=='oui') $puce = "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/actif-on-16.png' style='border:0' alt='"._T("noisetier:noisette_active")."'>";
	else $puce = "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/actif-off-16.png' style='border:0' alt='"._T("noisetier:noisette_non_active")."'>";
	$out .= "<div class='verdana1' style='float: $spip_lang_right; font-weight: bold;position:relative;display:inline;'>";
	if (autoriser('gerer','noisetier')){
		if($actif=='oui') $action_link = generer_action_auteur("noisetier_active","desactiver-$id_noisette",$redirect);
		else $action_link = generer_action_auteur("noisetier_active","activer-$id_noisette",$redirect);
		$action_link_noredir = parametre_url($action_link,'redirect','');
		$out .= "<form class='ajaxAction' method='POST' action='$action_link_noredir' style='border:0; margin:0; display:inline;' name='form_active_$id_noisette'>";
		$out .= form_hidden($action_link_noredir);
		$out .= "<input type='hidden' name='redirect' value='$redirect' />";
		$out .= "<input type='hidden' name='idtarget' value='noisette-$id_noisette' />";
		if ($actif=='oui') $title = _T('noisetier:desactiver');
		else $title = _T('noisetier:activer');
		$out .= "<a href='javascript: document.forms.form_active_$id_noisette.submit();' title='$title'>";
		$out .= $puce;
		$out .= "</a></form>";
	}
	else $out .= $puce;
	$out .= "</div>\n";

	// Titre de la noisette
	$out .= "<div style='padding: 0px 20px 2px 30px; color: black;'>";
	if ($noisette_visible==$id_noisette)
		$out .= bouton_block_depliable(typo($row['titre']),true,"block-noisette-$id_noisette");
	else
		$out .= bouton_block_depliable(typo($row['titre']),false,"block-noisette-$id_noisette");
	$out .= "<div style='font-size:90%;'>".typo($row['descriptif'])."</div></div>";
	if ($noisette_visible==$id_noisette)
		$out .= debut_block_depliable(true,"block-noisette-$id_noisette");
	else
		$out .= debut_block_depliable(false,"block-noisette-$id_noisette");
	
	//Infos sur la noisette
	$out .= "<div style='margin:5px 0; padding:5px 0; border-top:1px dashed #999;' class='serif'>";
	$out .= "<b class='serif'>id_noisette&nbsp;:</b> $id_noisette";
	if ($row['version']!='')
		$out .= " - <b class='serif'>"._T('noisetier:info_version')."</b> ".$row['version'];
	if ($row['auteur']!='')
		$out .= " - <b class='serif'>"._T('noisetier:info_auteur')."</b> ".$row['auteur'];
	if ($row['lien']!='')
		$out .= "<br /><b class='serif'>"._T('noisetier:info_lien')."</b> <a href='".$row['lien']." class='spip_url spip_out'>".couper($row['lien'],40)."</a>";
	$out .= "</div>";
	//Supression de la noisette  (faire un formulaire)
	if (autoriser('gerer','noisetier')) {
		$out .= icone_horizontale(_T('noisetier:supprimer_noisette'), "", "../"._DIR_PLUGIN_NOISETIER."/img_pack/noisette-24.png", "supprimer.gif",false);
	
	}
	$out .= fin_block();
	$out .= fin_cadre_relief(true);
	$out .= "</div>";
	
	return $out;
}

?>