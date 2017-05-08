<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// https://code.spip.net/@inc_legender_auteur_dist
function inc_legender_auteur_supp_dist($auteur)
{
	if (!$auteur['id_auteur']) {
		if (_request('new') == 'oui') {
			$new = true;
		} else {
			include_spip('inc/headers');
			redirige_par_entete(generer_url_ecrire('auteurs'));
		}
	}
	
	if (!$new) {
		if (autoriser('modifier', 'auteur', $auteur['id_auteur'])) {
		$auteur_infos_voir_supp = legender_auteur_supp_voir($auteur, $redirect);
		$corps = legender_auteur_supp_saisir($auteur, $auteur_infos_voir_supp, $redirect);
	} else {
		$corps = ' ';
	}
	
	}else {
		$corps = '';
}
	

	return $corps;
}

// La partie affichage du formulaire...
function legender_auteur_supp_saisir($auteur, $auteur_infos_voir_supp, $redirect){

	global $options, $connect_statut, $connect_id_auteur, $connect_toutes_rubriques;

	include_spip('inc/autoriser');

	$id_auteur = $auteur['id_auteur'];
	$id = $id_auteur;
	
	$setconnecte = ($connect_id_auteur == $id_auteur);


	// Elaborer le formulaire
	$corps_supp = '';
	$var_user['b.id'] = '0';
	$var_user['a.login'] = '0';
	foreach(lire_config('inscription2') as $cle => $val){
		if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle) and $cle != 'statut_nouveau'){
			$cle = ereg_replace("^username.*$", "login", $cle);
			$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
			if($cle == 'nom' or $cle == 'email' or $cle == 'login')
				$var_user['a.'.$cle] = '0';
			elseif(ereg("^statut_rel.*$", $cle))
				$var_user['b.statut_relances'] = '1';
			elseif($cle == 'pays'){
				$var_user['c.pays'] = '1';
				$var_user['c.id_pays as id_pays'] = '1';}
			elseif($cle == 'pays_pro'){
				$var_user['d.pays'] = '1';
				$var_user['d.pays as pays_pro'] = '1';
				$var_user['d.id_pays as id_pays_pro'] = '1';}
			else 
				$var_user['b.'.$cle] = '1';
		}elseif($cle=='newsletter' and $val != ''){
			$aux3 = array();
			$aux4 = array();
			$news = spip_query("select id_liste, titre from spip_listes");
			$listes = spip_query("select id_liste from spip_auteurs_listes where id_auteur = $id");
			while($q = spip_fetch_array($listes))
				$aux3[]=$q['id_liste'];
			while($q = spip_fetch_array($news))
				$aux4[] = $q;
		}
	}
	if($var_user['c.pays'] && $var_user['d.pays'])
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays left join spip_geo_pays d on b.pays_pro=d.id_pays where a.id_auteur= $id");
	elseif($var_user['c.pays'])
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays where a.id_auteur= $id");
	else
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur where a.id_auteur= $id");

	$query = spip_fetch_array($query);
	
	if($query['id'] == NULL){
		$id_elargi =spip_query("INSERT INTO spip_auteurs_elargis (id_auteur) VALUES ($id)");
		echo "chiotte 2";
	}
	
	foreach ($query as $cle => $val){
		
		if(($cle == 'id') || ($cle == 'id_pays') || ($cle == 'id_pays_pro') ||  ($cle == 'login') || ($cle == 'nom') || ($cle == 'email')){
			$corps_supp .= "<input type='hidden' id='$cle' name='$cle' value='$val'>";
		}
		elseif($cle == 'pays'){
			$corps_supp .= "<strong>"._T('inscription2:'.$cle)."</strong><br />"
				. "<select name='$cle' id='$cle' class='formo' style='width:auto'>"
				. "<option value=''>"._T('inscription2:pays')."</option>";
			include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
			foreach($liste_pays as $cle => $val){
				if ($cle == $query['id_pays'])
					$corps_supp .= "<option value='$cle' selected>$val</option>";
				else 
					$corps_supp .= "<option value='$cle'>$val</option>";
			}$corps_supp .= "</select>";
		}
		elseif($cle == 'pays_pro'){
			$corps_supp .= "<strong>"._T('inscription2:'.$cle)."</strong><br />"
				. "<select name='$cle' id='$cle' class='formo' style='width:auto'>"
				. "<option value=''>"._T('inscription2:pays')."</option>";
			include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
			foreach($liste_pays as $cle=> $val){
				if ($cle == $query['id_pays_pro'])
					$corps_supp .= "<option value='$cle' selected>$val</option>";
				else 
					$corps_supp .= "<option value='$cle'>$val</option>";
			}$corps_supp .= "</select>";
		}
		elseif ($cle == 'latitude'){
			if ($geomap_append_moveend_map = charger_fonction('geomap_append_clicable_map','inc',true)){
				$corps_supp .= "<br /><div class='geomap' id='map' style='width:100%;height:350px'> </div><br />";
				$corps_supp .= $geomap_append_moveend_map("map",'latitude','longitude',$query['latitude'],$query['longitude'], NULL,NULL,true);
			}
			$corps_supp .= "<strong>"._T('inscription2:'.$cle)."</strong><br />"
			. "<input type='text' id='$cle' name='$cle' class='formo' value='$val'><br />";
		}
		elseif($cle!= 'id_auteur' and $cle != 'statut_nouveau')
		$corps_supp .= "<strong>"._T('inscription2:'.$cle)."</strong><br />"
		. "<input type='text' id='$cle' name='$cle' class='formo' value='$val'><br />"; 
	}
	if($news){
		if ($aux4){
		$corps_supp .= "<strong>"._T('inscription2:newsletter')."</strong><br />"
		. "<select name='news[]' id='news' multiple>";
		foreach($aux4 as $val){
			if (in_array($val['id_liste'], $aux3))
				$corps_supp .= "<option value='".$val['id_liste']."' selected>".$val['titre']."</option>";
			else 
				$corps_supp .= "<option value='".$val['id_liste']."'>".$val['titre']."</option>";
		}
		$corps_supp .= "</select><br/><a onclick=\"$('#news').find('option').attr('selected', false);\">"._T('inscription2:deselect_listes')."</a> </small><br /></td></tr>";
		}
	}
		$corps_supp .= "\n<br />";


	//
	// Retour
	//

	$corps_supp = $auteur_infos_voir_supp
		. "<div id='auteur_infos_edit_supp'>\n"
		. '<div>&nbsp;</div>'
		. "\n<div class='serif'>"
		. debut_cadre_relief("fiche-perso-24.gif",
			true, "", _T("icone_informations_personnelles"))
		. $corps_supp
		. fin_cadre_relief(true)
		. "</div>\n" # /serif
		. "</div>\n"; # /auteur_infos_edit

	// Installer la fiche "auteur_infos_voir"
	// et masquer le formulaire si on n'en a pas besoin
	$new = ($auteur_infos_voir_supp == '');
	if (!$new
	AND !_request('echec')
	AND !_request('edit')) {
		$corps_supp .= "<script>jQuery('#auteur_infos_edit_supp').hide()</script>\n";
	} else {
		$corps_supp .= "<script>jQuery('#auteur_infos_voir_supp').hide()</script>\n";
	}

	// Redirection apres enregistrement ?
	if ($redirect)
		$corps_supp .= "<input type='hidden' name='redirect' value=\"".attribut_html($redirect)."\" />\n";

	$corps_supp .= "<div style='text-align: right'><input type='submit' value='"._T('bouton_enregistrer')."' class='fondo' /></div>";

	$arg = intval($id_auteur);
	$ret .= generer_action_auteur('editer_auteur_supp', $arg, $redirect, $corps_supp, ' method="POST"');

	return $ret;
}

// L'affichage des infos supplémentaires...
function legender_auteur_supp_voir($auteur, $redirect)
{
	global $connect_toutes_rubriques, $connect_statut, $connect_id_auteur, $champs_extra, $options, $spip_lang_right;
	$res = "";

	if (!$id_auteur = $auteur['id_auteur']) {
		$new = true;
	}

	// Bouton "modifier" ?
	if (autoriser('modifier', 'auteur', $id_auteur)) {
		$res .= "<span id='bouton_modifier_auteur_supp'>";

		if (_request('edit_supp') == 'oui') {
			$clic = _T('icone_retour');
			$retour = _T('inscription2:admin_modifier_auteur_supp');
		} else {
			$clic = _T('inscription2:admin_modifier_auteur_supp');
			$retour = _T('icone_retour');
		}

		$h = generer_url_ecrire("auteur_infos","id_auteur=$id_auteur&edit_supp=oui");
		$h = "<a\nhref='$h'>$clic</a>";
		$res .= icone_inline($clic, $h, "redacteurs-24.gif", "edit.gif", $spip_lang_right);

		$res .= "<script type='text/javascript'><!--
		var intitule_bouton = "._q($retour).";
		jQuery('#bouton_modifier_auteur_supp a')
		.click(function() {
			jQuery('#auteur_infos_edit_supp')
			.toggle();
			jQuery('#auteur_infos_voir_supp')
			.toggle();
			jQuery('#bouton_modifier_auteur_supp > a > span')
			.each(function(){
				var tmp = jQuery(this).html();
				jQuery(this).html(intitule_bouton);
				intitule_bouton = tmp;
			});
			return false;
		});
		// --></script>\n";
		$res .= "</span>\n";
	}
	
	$res .= gros_titre('Inscription2','',false);

	$res .= "<div class='nettoyeur'></div>";
	$res .= "<div id='auteur_infos_voir_supp'>";

	$id = _request('id_auteur');
	
	$var_user['a.id_auteur'] = '0';
	foreach(lire_config('inscription2') as $cle => $val){
		if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle) and $cle != 'statut_nouveau'){
			$cle = ereg_replace("^username.*$", "login", $cle);
			$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
			if($cle == 'nom' or $cle == 'email' or $cle == 'login' or $cle == 'id_auteur')
				$var_user['a.'.$cle] = '0';
			elseif(ereg("^statut_rel.*$", $cle))
				$var_user['b.statut_relances'] = '1';
			elseif($cle == 'pays'){
				$var_user['c.pays'] = '1';
			}
			elseif($cle == 'pays_pro'){
				$var_user['d.pays'] = '1';
				$var_user['d.pays as pays_pro'] = '1';}
			else 
				$var_user['b.'.$cle] = '1';
		}
		elseif($cle=='newsletter' and $val != ''){
			$aux3 = array();
			$aux4 = array();
			$news = spip_query("select id_liste, titre from spip_listes");
			$listes = spip_query("select id_liste from spip_auteurs_listes where id_auteur = $id");
			while($q = spip_fetch_array($listes))
				$aux3[]=$q['id_liste'];
			while($q = spip_fetch_array($news))
				$aux4[] = $q;
		}
	}
	if($var_user['c.pays'] && $var_user['d.pays'])
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays left join spip_geo_pays d on b.pays_pro=d.id_pays where a.id_auteur= $id");

	elseif($var_user['c.pays'])
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays where a.id_auteur= $id");

	else
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur where a.id_auteur= $id");

	$query = spip_fetch_array($query);
	
	if($query['id_auteur'] == NULL){
		$id_elargi =spip_query("INSERT INTO spip_auteurs_elargis (id_auteur) VALUES ($id)");
	}
	//Debut de l'affichage des données...
	foreach ($query as $cle => $val){
		if(($cle == 'id_auteur') || ($cle == 'login') || ($cle == 'nom') || ($cle == 'email') || ($cle == 'id_pays') || ($cle == 'id_pays_pro'))
			continue;
		elseif (strlen($val) >= 1){ $res .= "<p><strong>"._T('inscription2:'.$cle)." : </strong>" . $val . "</p>"; }
	}
	if($aux4 and $aux3){
		$res .= "<strong>"._T('inscription2:newsletter')."</strong><br />"
		. "<ul>";
		foreach($aux4 as $val){
			if (in_array($val['id_liste'], $aux3))
				$res .= "<li>".$val['titre']."</li>";
		}
		$res .= "</ul>";
	}
	
	$res .= "</div>\n";

	return $res;
}
?>