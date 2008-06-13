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
include_spip('inc/filtres');
include_spip('inc/acces');
include_spip('base/abstract_sql');

function action_editer_auteur_supp_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		$r = "action_editer_auteur_supp_dist $arg pas compris";
		spip_log($r);
       } else {
		$url = action_editer_auteur_supp_post($r);
		redirige_par_entete($url);
	}
}

// http://doc.spip.org/@action_legender_post
function action_editer_auteur_supp_post($r){
	global $auteur_session;

	$echec = array();
	
	$redirect = _request('redirect');

		list($tout, $id_auteur, $ajouter_id_article,$x,$s) = $r;

		foreach(lire_config('inscription2') as $cle => $val){
			if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle)){
				$cle = ereg_replace("^username.*$", "login", $cle);
				$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
				if($cle == 'nom' or $cle == 'email' or $cle == 'login')
					$var_user['a.'.$cle] =  '`'.$cle.'` = '.sql_quote(_request($cle)).'';
				elseif($cle == 'statut_nouveau'){
				}
				elseif(ereg("^statut_rel.*$", $cle))
					$var_user['b.statut_relances'] =  '`statut_relances` = '.sql_quote($_POST['statut_relances']).'';
				else
					$var_user['b.'.$cle] =  '`'.$cle.'` = '.sql_quote(_request($cle)).'';
			}
			elseif ($val!='' and $cle == 'accesrestreint'){
				$aux = spip_query("select id_zone from spip_zones_auteurs where id_auteur = $id_auteur");
				while($q = spip_fetch_array($aux))
					$acces[]=$q['id_zone'];
				$acces_array = $_POST['acces'];
				if(!empty($acces) and empty($acces_array))
					spip_query("delete from spip_zones_auteurs where id_auteur = $id_auteur");
				elseif(empty($acces) and !empty($acces_array))
					spip_query("insert into spip_zones_auteurs (id_zone, id_auteur) values (".join(", $id_auteur), (", $acces_array).", $id_auteur)");
				elseif(!empty($acces) and !empty($acces_array)){
					$diff1 = array_diff($acces_array, $acces);
					$diff2 = array_diff($acces, $acces_array);
					if (!empty($diff1))
						spip_query("insert into spip_zones_auteurs (id_zone, id_auteur) values (".join(", $id_auteur), (", $diff1).", $id_auteur)");
					if(!empty($diff2))
						foreach($diff2 as $val)
							spip_query("delete from spip_zones_auteurs where id_auteur= $id_auteur and id_zone = $val");
				}
			}
			elseif ($val!='' and $cle == 'newsletter'){
				$aux = spip_query("select id_liste from spip_auteurs_listes where id_auteur = $id_auteur");
				while($q = spip_fetch_array($aux))
					$listes[]=$q['id_liste'];
				$listes_array = _request('news');
				if(!empty($listes) and empty($listes_array))
					sql_delete("spip_auteurs_listes","$id_auteur");
				elseif(empty($listes) and !empty($listes_array))
					spip_query("insert into spip_auteurs_listes (id_liste, id_auteur) values (".join(", $id_auteur), (", $listes_array).", $id_auteur)");
				elseif(!empty($listes) and !empty($listes_array)){
					$diff1 = array_diff($listes_array, $listes);
					$diff2 = array_diff($listes, $listes_array);
					if (!empty($diff1))
						spip_query("insert into spip_auteurs_listes (id_liste, id_auteur) values (".join(", $id_auteur), (", $diff1).", $id_auteur)");
					if(!empty($diff2))
						foreach($diff2 as $val)
							sql_delete("spip_auteurs_listes","$id_auteur and id_liste = $val");
				}
			}
		}
	
		spip_query("update spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur set ".join(', ', $var_user)." where a.`id_auteur`='$id_auteur'");
		// if (!$n) die('UPDATE FAILED '. $id_auteur .'');

	// il faudrait rajouter OR $echec mais il y a conflit avec Ajax
	
	// Notifications, gestion des revisions, reindexation...
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_auteurs_elargis',
				'id_objet' => $id_auteur
			),
			'data' => $auteur
		)
	);
	
	// ..et mettre a jour les fichiers .htpasswd et .htpasswd-admin
	ecrire_acces();

	$echec = $echec ? '&echec=' . join('@@@', $echec) : '';

	$redirect = rawurldecode($redirect);
	if ($echec) {
		// revenir au formulaire de saisie
		$ret = !$redirect
			? '' 
			: ('&redirect=' . rawurlencode($redirect));

		return generer_url_ecrire('auteur_infos',
			"id_auteur=$id_auteur$echec$ret",'&');
	} else {
		// modif: renvoyer le resultat ou a nouveau le formulaire si erreur
		if (!$redirect)
			$redirect = generer_url_ecrire("auteur_infos", "id_auteur=$id_auteur", '&', true);

		return $redirect;
	}
}

?>