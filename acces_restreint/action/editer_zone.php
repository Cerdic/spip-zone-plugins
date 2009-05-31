<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

/**
 * editer une zone (action apres creation/modif de zone)
 *
 * @return array
 */
function action_editer_zone_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_article n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_zone = intval($arg)) {
		if (!$id_zone = accesrestreint_action_insert_zone())
			return array(false,_L('echec'));
		// ajouter les droits a l'admin si demande, lors de la creation
		if (_request('droits_admin')){
			accesrestreint_revision_zone_objets_lies($id_zone,$GLOBALS['visiteur_session']['id_auteur'],'auteur');
		}
	}
	
	$err = action_zone_set($id_zone);
	return array($id_zone,$err);
}

/**
 * mettre a jour une zone
 *
 * @param int $id_zone
 * @return string
 */
function action_zone_set($id_zone){
	$err = '';

	$c = array();
	foreach (array(
		'titre', 'descriptif',
	) as $champ)
		$c[$champ] = _request($champ);
	foreach (array(
		'publique', 'privee'
	) as $champ)
		$c[$champ] = _request($champ)=='oui'?'oui':'non';

	include_spip('inc/modifier');
	accesrestreint_revision_zone($id_zone, $c);
	accesrestreint_revision_zone_objets_lies($id_zone, _request('rubriques'),'rubrique','set');

	return $err;
}


/**
 * Mettre a jour les liens objets/zones.
 * si zones vaut '', associe toutes les zones a(aux) objets(s).
 * $ids est une liste d'id.
 * $type est le type de l'objet (rubrique, auteur).
 * $operation = add/set/del pour ajouter, affecter uniquement, ou supprimer les objets listes dans ids.
 *
 * @param int/array $zones
 * @param int/array $ids
 * @param string $type
 */
function accesrestreint_revision_zone_objets_lies($zones,$ids,$type,$operation = 'add'){
	$in = "";
	if ($zones){
		$in = sql_in('id_zone',$zones);
	}
	$liste = sql_allfetsel('id_zone','spip_zones',$in);
	foreach($liste as $row){
		if ($operation=='del'){
			// on supprime les ids listes
			sql_delete("spip_zones_{$type}s",array("id_zone=".intval($row['id_zone']),sql_in("id_$type",$ids)));			
		}
		else {
			if (!$ids) $ids = array();
			elseif (!is_array($ids)) $ids = array($ids);
			// si c'est une affectation exhaustive, supprimer les existants qui ne sont pas dans ids
			// si c'est un ajout, ne rien effacer
			if ($operation=='set')
				sql_delete("spip_zones_{$type}s",array("id_zone=".intval($row['id_zone']),sql_in("id_$type",$ids,"NOT")));
			$deja = array_map('reset',sql_allfetsel("id_$type","spip_zones_{$type}s","id_zone=".intval($row['id_zone'])));
			$add = array_diff($ids,$deja);
			foreach ($add as $id) {
				if (autoriser('affecterzone',$type,$id,null,array('id_zone'=>$row['id_zone'])))
					sql_insertq("spip_zones_{$type}s",array('id_zone'=>$row['id_zone'],"id_$type"=>intval($id)));
			}
		}
	}	
}

/**
 * Creer une nouvelle zone
 *
 * @return int
 */
function accesrestreint_action_insert_zone(){
	include_spip('inc/autoriser');
	if (!autoriser('creer','zone'))
		return false;
	// nouvel zone
	$id_zone = sql_insertq("spip_zones", array("maj"=>"NOW()", 'publique'=>'non','privee'=>'non'));

	if (!$id_zone){
		spip_log("accesrestreint action : impossible d'ajouter un zone");
		return false;
	} 
	return $id_zone;	
}

/**
 * Enregistre la revision d'une zone
 *
 * @param int $id_zone
 * @param array $c
 * @return string
 */
function accesrestreint_revision_zone($id_zone, $c=false) {

	modifier_contenu('zone', $id_zone,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * Supprimer une zone
 *
 * @param unknown_type $supp_zone
 * @return unknown
 */
function accesrestreint_supprime_zone($id_zone){
	$supp_zone = sql_getfetsel("id_zone", "spip_zones", "id_zone=" . intval($id_zone));
	if (intval($id_zone) AND 	intval($id_zone) == intval($supp_zone)){
		// d'abord les auteurs
		sql_delete("spip_zones_auteurs", "id_zone=".intval($id_zone));
		// puis la portee
		sql_delete("spip_zones_rubriques", "id_zone=".intval($id_zone));
		// puis la zone
		sql_delete("spip_zones", "id_zone=".intval($id_zone));
	}
	$id_zone = 0;
	return $id_zone;
}


?>