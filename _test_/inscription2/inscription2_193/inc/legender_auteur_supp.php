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
	spip_log('saisir les infos de l auteur='.$auteur);
	$id_auteur = $auteur;
	$corps_supp = '<li class="editer_inscription2 fieldset">';
	$corps_supp .= '<fieldset><h3 class="legend">Inscription 2</h3>';
	$corps_supp .= '<ul>';
	
	// Elaborer le formulaire
	$var_user['b.id_auteur'] = $auteur;
	$var_user['a.login'] = '0';
	foreach(lire_config('inscription2',array()) as $cle => $val){
		if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle) and $cle != 'statut_nouveau'){
			$cle = ereg_replace("^username.*$", "login", $cle);
			$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
			if($cle == 'nom' or $cle == 'email' or $cle == 'login')
				$var_user['a.'.$cle] = '0';
			elseif(ereg("^statut_rel.*$", $cle)){
				$var_user['b.statut_relances'] = '1';
				$champs[$cle] = '';
			}
			else{
				$var_user['b.'.$cle] = '1';
				$champs[$cle] = '';
			}
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
	
	$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur","a.id_auteur= $id_auteur");

	$query = sql_fetch($query);
	if($query == NULL){
		$query = $champs;
	}

	foreach ($query as $cle => $val){
		if(($cle == 'login') || ($cle == 'nom') || ($cle == 'email')){
		}
		elseif ($cle == 'latitude'){
			if ($geomap_append_moveend_map = charger_fonction('geomap_append_clicable_map','inc',true)){
				$corps_supp .= "<li><div class='geomap' id='map' style='width:100%;height:350px'> </div>";
				$corps_supp .= $geomap_append_moveend_map("map",'latitude','longitude',$query['latitude'],$query['longitude'], NULL,NULL,true);
				$corps_supp .= "</li>";
			}
			$corps_supp .= "<li><label>"._T('inscription2:'.$cle)."</label>"
			. "<input type='text' id='$cle' name='$cle' class='text' value='$val' /></li>";
		}
		elseif($cle!= 'id_auteur' and $cle != 'statut_nouveau'){
			if(find_in_path('prive/inscription2_champs_'.$cle.'.html')){
				$corps_supp .= recuperer_fond('prive/inscription2_champs_'.$cle,array('cle'=>$cle,'val'=>$val));
			}else{
				$corps_supp .= "\n<li><label>"._T('inscription2:'.$cle)."</label>"
				. "<input type='text' id='$cle' name='$cle' class='text' value='".$val."' /></li>";
			}
		}
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
		$corps_supp .= "\n</ul>";
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
	foreach(lire_config('inscription2',array()) as $cle => $val){
		if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle) and $cle != 'statut_nouveau'){
			$cle = ereg_replace("^username.*$", "login", $cle);
			$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
			if($cle == 'nom' or $cle == 'email' or $cle == 'login' or $cle == 'id_auteur')
				$var_user['a.'.$cle] = '0';
			elseif(ereg("^statut_rel.*$", $cle))
				$var_user['b.statut_relances'] = '1';
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
	$query = sql_select(join(', ', array_keys($var_user)),"spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur","a.id_auteur= $id_auteur");

	$query = sql_fetch($query);
	
	if($query['id_auteur'] == NULL){
		$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=>$id_auteur));
	}
	//Debut de l'affichage des données...
	foreach ($query as $cle => $val){
		if(($cle == 'id_auteur') || ($cle == 'login') || ($cle == 'nom') || ($cle == 'email') || ($cle == 'id_pays') || ($cle == 'id_pays_pro'))
			continue;
		elseif (strlen($val) >= 1){
			if(find_in_path('prive/inscription2_vue_'.$cle.'.html')){
				$res .= recuperer_fond('prive/inscription2_vue_'.$cle,array('cle'=>$cle,'val'=>$val));
			}else{
				$res .= "<p><strong>"._T('inscription2:'.$cle)." : </strong>" . typo($val) . "</p>";	
			}
		}
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