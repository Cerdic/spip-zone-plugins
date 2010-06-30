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

// La partie affichage du formulaire...
function legender_auteur_supp_saisir($id_auteur){
	global $spip_version_branche;
	if ($spip_version_branche>='2.1'){
		include_spip('inc/plugin');
		actualise_plugins_actifs();
	}
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());

	$corps_supp = '<li class="editer_inscription2 fieldset">';
	$corps_supp .= '<fieldset><h3 class="legend">Inscription 2</h3>';
	$corps_supp .= '<ul>';

	$champs = array();
	// Elaborer le formulaire
	$var_user[] = 'b.id_auteur';
	foreach(lire_config('inscription2',array()) as $cle => $val){
		$cle = preg_replace("/_(obligatoire|fiche|table).*$/", "", $cle);
		if($val=='on' AND !in_array($cle,$exceptions_des_champs_auteurs_elargis) and !preg_match("/^(categories|zone|newsletter).*$/", $cle) ){
			$var_user[] = 'b.'.$cle;
			$champs[$cle] = '';
		}
	}

	// id_auteur numerique et different de '0' !
	if (is_numeric($id_auteur) and $id_auteur){
		$query = sql_fetsel($var_user,"spip_auteurs a left join spip_auteurs_elargis b USING(id_auteur)","a.id_auteur='$id_auteur'");
	} else {
		$query = $champs;
	}

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

?>
