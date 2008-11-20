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

function inc_legender_auteur_supp_dist($auteur){
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
		}
	}
	return $auteur_infos_voir_supp;
}
// La partie affichage du formulaire...
function legender_auteur_supp_saisir($auteur){
	$id_auteur = $auteur['id_auteur'];
	
	$corps_supp = '<li class="editer_inscription2 fieldset">';
	$corps_supp .= '<fieldset><h3 class="legend">Inscription 2</h3>';
	$corps_supp .= '<ul>';
	
	// Elaborer le formulaire
	$var_user['b.id_auteur'] = '0';
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
			$news = sql_select("id_liste, titre","spip_listes");
			$listes = sql_select("id_liste","spip_auteurs_listes","id_auteur = $id");
			while($q = sql_fetch($listes))
				$aux3[]=$q['id_liste'];
			while($q = sql_fetch($news))
				$aux4[] = $q;
		}
	}
	if($var_user['c.pays'] && $var_user['d.pays'])
		$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays left join spip_geo_pays d on b.pays_pro=d.id_pays","a.id_auteur= $id_auteur");
	elseif($var_user['c.pays'])
		$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays","a.id_auteur= $id_auteur");
	else
		$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur","a.id_auteur= $id_auteur");

	$query = sql_fetch($query);
	if($query['id_auteur'] == NULL){
		$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=> $id_auteur));
	}
	
	foreach ($query as $cle => $val){
		if(($cle == 'id_pays') || ($cle == 'id_pays_pro') ||  ($cle == 'login') || ($cle == 'nom') || ($cle == 'email')){
			$corps_supp .= "<input type='hidden' id='$cle' name='$cle' value='$val' />";
		}
		elseif($cle == 'pays'){
			$corps_supp .= "<li><label>"._T('inscription2:'.$cle)."</label>"
				. "<select name='$cle' id='$cle' class='text' style='width:auto'>"
				. "<option value=''>"._T('inscription2:pays')."</option>";
			include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
			foreach($liste_pays as $cle => $val){
				if ($cle == $query['id_pays'])
					$corps_supp .= "<option value='$cle' selected>$val</option>";
				else 
					$corps_supp .= "<option value='$cle'>$val</option>";
			}$corps_supp .= "</select></li>";
		}
		elseif($cle == 'pays_pro'){
			$corps_supp .= "<li><label>"._T('inscription2:'.$cle)."</label>"
				. "<select name='$cle' id='$cle' class='text' style='width:auto'>"
				. "<option value=''>"._T('inscription2:pays')."</option>";
			include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
			foreach($liste_pays as $cle=> $val){
				if ($cle == $query['id_pays_pro'])
					$corps_supp .= "<option value='$cle' selected>$val</option>";
				else 
					$corps_supp .= "<option value='$cle'>$val</option>";
			}$corps_supp .= "</select></li>";
		}
		elseif ($cle == 'latitude'){
			if ($geomap_append_moveend_map = charger_fonction('geomap_append_clicable_map','inc',true)){
				$corps_supp .= "<br /><div class='geomap' id='map' style='width:100%;height:350px'> </div><br />";
				$corps_supp .= $geomap_append_moveend_map("map",'latitude','longitude',$query['latitude'],$query['longitude'], NULL,NULL,true);
			}
			$corps_supp .= "<li><label>"._T('inscription2:'.$cle)."</label>"
			. "<input type='text' id='$cle' name='$cle' class='text' value='$val' /></li>";
		}
		elseif($cle!= 'id_auteur' and $cle != 'statut_nouveau')
		$corps_supp .= "<li><label>"._T('inscription2:'.$cle)."</label>"
		. "<input type='text' id='$cle' name='$cle' class='text' value='".typo($val)."' /><li>"; 
	}
	if($news){
		if ($aux4){
		$corps_supp .= "<li><label>"._T('inscription2:newsletter')."</label>"
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
		$corps_supp .= "\n</li></ul>";
		$corps_supp .= "\n</fieldset></li>";
	return $corps_supp;
}

// L'affichage des infos supplémentaires...
function legender_auteur_supp_voir($auteur)
{	
	$res = "<h2 class='titrem'>Inscription2</h2>";

	$res .= "<div class='nettoyeur'></div>";
	$res .= "<div id='auteur_infos_voir_supp'>";

	$id_auteur = _request('id_auteur');
	
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
			$news = sql_select("id_liste, titre","spip_listes");
			$listes = sql_select("id_liste","spip_auteurs_listes","id_auteur = $id");
			while($q = sql_fetch($listes))
				$aux3[]=$q['id_liste'];
			while($q = sql_fetch($news))
				$aux4[] = $q;
		}
	}
	if($var_user['c.pays'] && $var_user['d.pays'])
		$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays left join spip_geo_pays d on b.pays_pro=d.id_pays","a.id_auteur= $id_auteur");

	elseif($var_user['c.pays'])
		$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_geo_pays c on b.pays=c.id_pays","a.id_auteur= $id_auteur");

	else
		$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur","a.id_auteur= $id_auteur");

	$query = sql_fetch($query);
	
	if($query['id_auteur'] == NULL){
		$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=>$id_auteur));
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