<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');

function action_editer_gis_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// si id_gis n'est pas un nombre, c'est une creation
	if (!$id_gis = intval($arg)) {
		if (!autoriser('creer','gis') or !$id_gis = gis_inserer())
			return array(false,_L('echec'));
	}
	$err = gis_modifier($id_gis);
	return array($id_gis,$err);
}

/**
 * Fonction d'insertion d'un gis vide
 * 
 * @return int/false $id_gis : l'identifiant numérique du point ou false en cas de non création
 */
function gis_inserer() {
	$champs = array();
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_gis',
		),
		'data' => $champs
	));
	
	$id_gis = sql_insertq("spip_gis", $champs);
	
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_gis',
				'id_objet' => $id_gis
			),
			'data' => $champs
		)
	);
	return $id_gis;
}

/**
 *  Enregistrer certaines modifications d'un gis
 * 
 * @param int $id_gis : l'identifiant numérique du point
 * @param array $c : un array des valeurs à mettre en base (par défaut false, on récupère les valeurs passées en dans le POST)
 */
/**
 * Appelle toutes les fonctions de modification d'un point gis
 * $err est de la forme chaine de langue ou vide si pas d'erreur
 * http://doc.spip.org/@articles_set
 *
 * @param  $id_gis
 * @param null $set
 * @return string
 */
function gis_modifier($id_gis, $set=null) {
	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('gis','champs_editables'),
		// black list
		array('id_objet','objet'),
		// donnees eventuellement fournies
		$set
	);

	if(isset($c['lon'])){
		if($c['lon'] > 180){
			while($c['lon'] > 180){
				$c['lon'] = $c['lon'] - 360;
			}
		}else if($c['lon'] <= -180){
			while($c['lon'] <= -180){
				$c['lon'] = $c['lon'] + 360;
			}
		}
	}
	if(isset($c['lat'])){
		if($c['lat'] > 90){
			while($c['lat'] > 90){
				$c['lat'] = $c['lat'] - 180;
			}
		}else if($c['lat'] <= -90){
			while($c['lat'] <= -90){
				$c['lat'] = $c['lon'] + 180;
			}
		}
	}
	if ($err = objet_modifier_champs('gis', $id_gis,
		array(
			//'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='gis/$id_gis'",
		),
		$c))
		return $err;

	// lier a un parent ?
	$c = collecter_requests(array('id_objet', 'objet'),array(),$set);
	if (isset($c['id_objet']) AND intval($c['id_objet']) AND isset($c['objet']) AND $c['objet']) {
		lier_gis($id_gis, $c['objet'], $c['id_objet']);
	}

	return $err;
}


/**
 * Associer un point géolocalisé a des objets listes sous forme
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 *
 * on peut passer optionnellement une qualification du (des) lien(s) qui sera
 * alors appliquee dans la foulee.
 * En cas de lot de liens, c'est la meme qualification qui est appliquee a tous
 *
 * @param int $id_gis
 * @param array $objets
 * @param array $qualif
 * @return string
 */
function gis_associer($id_gis,$objets, $qualif = null){
	include_spip('action/editer_liens');
	$res = objet_associer(array('gis'=>$id_gis), $objets, $qualif);
	include_spip('inc/invalideur');
	suivre_invalideur("id='gis/$id_gis'");
	return $res;
}

/**
 * Dossocier un point géolocalisé des objets listes sous forme
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 *
 * un * pour $id_auteur,$objet,$id_objet permet de traiter par lot
 *
 * @param int $id_gis
 * @param array $objets
 * @return string
 */
function gis_dissocier($id_gis,$objets){
	include_spip('action/editer_liens');
	$res = objet_dissocier(array('gis'=>$id_gis), $objets);
	include_spip('inc/invalideur');
	suivre_invalideur("id='gis/$id_gis'");
	return $res;
}



/**
 * Supprimer définitivement un point géolocalisé
 * 
 * @param int $id_gis identifiant numérique du point
 * @return int|false 0 si réussite, false dans le cas ou le point n'existe pas
 */
function gis_supprimer($id_gis){
	$valide = sql_getfetsel('id_gis','spip_gis','id_gis='.intval($id_gis));
	if($valide && autoriser('supprimer','gis',$valide)){
		sql_delete("spip_gis_liens", "id_gis=".intval($id_gis));
		sql_delete("spip_gis", "id_gis=".intval($id_gis));
		$id_gis = 0;
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_gis/$id_gis'");
		return $id_gis;
	}
	return false;
}


/**
 * Délier un point géolocalisé d'un objet SPIP
 *
 * @param int $id_gis identifiant numérique du point
 * @param string $objet Le type de l'objet à lier
 * @param int $id_objet L'identifiant numérique de l'objet lié
 *
 * @return bool : true si la suppression de la liaison s'est bien passée, false à l'inverse
 */
function delier_gis($id_gis, $objet, $id_objet){
	//$objet = objet_type($objet);
	if ($id_objet AND $id_gis
	AND preg_match('/^[a-z0-9_]+$/i', $objet) # securite
	AND autoriser('delier','gis',$id_gis,$GLOBALS['visiteur_session'],array('objet' => $objet,'id_objet'=>$id_objet))
	) {
		gis_dissocier($id_gis,array($objet=>$id_objet));
		return true;
	}
	return false;
}

/**
 * Lier un point géolocalisé à un objet SPIP
 *
 * @param int $id_gis identifiant numérique du point
 * @param string $objet Le type de l'objet à lier
 * @param int $id_objet L'identifiant numérique de l'objet lié
 *
 * @return bool : true si la liaison s'est bien passée, false à l'inverse
 */
function lier_gis($id_gis, $objet, $id_objet){
	//$objet = objet_type($objet);
	if ($id_objet AND $id_gis
	AND preg_match('/^[a-z0-9_]+$/i', $objet) # securite
	AND !sql_getfetsel("id_gis", "spip_gis_liens", "id_gis=$id_gis AND id_objet=$id_objet AND objet=".sql_quote($objet))
	AND autoriser('lier','gis',$id_gis,$GLOBALS['visiteur_session'],array('objet' => $objet,'id_objet'=>$id_objet))
	) {
		gis_associer($id_gis,array($objet=>$id_objet));
		return true;
	}
	return false;
}

function insert_gis() {return gis_inserer();}
function revisions_gis($id_gis, $c=false) {return gis_modifier($id_gis,$c);}
function supprimer_gis($id_gis){return gis_supprimer($id_gis);}

?>
