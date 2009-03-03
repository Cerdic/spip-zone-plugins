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

function inc_legender_auteur_supp_dist($id_auteur){
	if (!$id_auteur) {
		if (_request('new') == 'oui') {
			$new = true;
		} else {
			include_spip('inc/headers');
			redirige_par_entete(generer_url_ecrire('auteurs'));
		}
	}
   
	if (!$new) {
		if (autoriser('modifier', 'auteur', $id_auteur)) {
			$auteur_infos_voir_supp = legender_auteur_supp_voir($id_auteur, $redirect);
		}
	}
	return $auteur_infos_voir_supp;
}
// La partie affichage du formulaire...
function legender_auteur_supp_saisir($id_auteur){
	$exceptions_des_champs_auteurs_elargis = pipeline('I2_exceptions_des_champs_auteurs_elargis',array());

	spip_log('INSCRIPTION 2 : saisir les infos de l auteur='.$id_auteur);
	
	$corps_supp = '<li class="editer_inscription2 fieldset">';
	$corps_supp .= '<fieldset><h3 class="legend">Inscription 2</h3>';
	$corps_supp .= '<ul>';
	
	// Elaborer le formulaire
	$var_user[] = 'b.id_auteur';
	foreach(lire_config('inscription2',array()) as $cle => $val){
		$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
		if($val=='on' AND !in_array($cle,$exceptions_des_champs_auteurs_elargis) and !ereg("^(categories|zone|newsletter).*$", $cle) ){
			$var_user[] = 'b.'.$cle;
			$champs[$cle];
		}
	}

	$query = sql_fetsel($var_user,"spip_auteurs a left join spip_auteurs_elargis b USING(id_auteur)","a.id_auteur='$id_auteur'");

	foreach ($query as $cle => $val){
		if(($cle!= 'id_auteur') AND !in_array($cle,$exceptions_des_champs_auteurs_elargis)){
			if(find_in_path('prive/inscription2_champs_'.$cle.'.html')){
				$corps_supp .= recuperer_fond('prive/inscription2_champs_'.$cle,array('cle'=>$cle,'val'=>$val,'id_auteur' => $id_auteur));
			}else{
				$corps_supp .= "\n<li><label>"._T('inscription2:'.$cle)."</label>"
				. "<input type='text' id='$cle' name='$cle' class='text' value=\"".entites_html($val)."\" /></li>";
			}
		}
	}
	$corps_supp .= "\n</ul>";
	$corps_supp .= "\n</fieldset>\n</li>";
	return $corps_supp;
}

// L'affichage des infos supplémentaires...
function legender_auteur_supp_voir($id_auteur){
	$exceptions_des_champs_auteurs_elargis = pipeline('I2_exceptions_des_champs_auteurs_elargis',array());
	
	$res = "<h2 class='titrem'>Inscription2</h2>";

	$res .= "<div class='nettoyeur'></div>";
	$res .= "<div id='auteur_infos_voir_supp'>";

	$id_auteur = _request('id_auteur');
	
	$var_user[] = 'a.id_auteur';
	foreach(lire_config('inscription2',array()) as $cle => $val){
		$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
		if($val == 'on' AND !in_array($cle,$exceptions_des_champs_auteurs_elargis) and !ereg("^(categories|zone|newsletter).*$", $cle) ){
			$var_user[] = 'b.'.$cle;
		}
	}
	$query = sql_fetsel($var_user,"spip_auteurs a left join spip_auteurs_elargis b USING(id_auteur)","a.id_auteur= $id_auteur");
	
	if($query['id_auteur'] == NULL){
		$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=>$id_auteur));
	}
	
	if(is_array($query)){
		//Debut de l'affichage des données...
		foreach ($query as $cle => $val){
			if (!in_array($cle,$exceptions_des_champs_auteurs_elargis) AND (strlen($val) >= 1)){
				if(find_in_path('prive/inscription2_vue_'.$cle.'.html')){
					$res .= recuperer_fond('prive/inscription2_vue_'.$cle,array('cle'=>$cle,'val'=>$val,'id_auteur' => $id_auteur));
				}else{
					$res .= "<p><strong>"._T('inscription2:'.$cle)." : </strong>" . typo($val) . "</p>";	
				}
			}
		}
	}
	$res .= "</div>\n";

	return $res;
}
?>