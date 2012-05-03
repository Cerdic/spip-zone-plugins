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
		if (!$id_gis = insert_gis())
			return array(false,_L('echec'));
	}
	$err = revisions_gis($id_gis);
	return array($id_gis,$err);
}

/**
 * Fonction d'insertion d'un gis vide
 * 
 * @return int/false $id_gis : l'identifiant numérique du point ou false en cas de non création
 */
function insert_gis() {
	if(autoriser('creer','gis')){
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
	}else{
		return false;
	}
}

/**
 *  Enregistrer certaines modifications d'un gis
 * 
 * @param int $id_gis : l'identifiant numérique du point
 * @param array $c : un array des valeurs à mettre en base (par défaut false, on récupère les valeurs passées en dans le POST)
 */
function revisions_gis($id_gis, $c=false) {
	$err = '';
	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
			'lat', 'lon', 'zoom', 'titre', 'descriptif', 'adresse', 'code_postal', 'ville', 'region', 'pays'
		) as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('gis', $id_gis, array(
			//'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='id_gis/$id_gis'"
		),
		$c);
	
	if ((intval(_request('id_objet')) && _request('objet')) OR (intval($c['id_objet']) && $c['objet'])) {
		$objet = _request('objet') ? _request('objet') : $c['objet'];
		$id_objet = _request('id_objet') ? _request('id_objet') : $c['id_objet'];
		lier_gis($id_gis, $objet, $id_objet);
	}
	
	return $err;
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
		sql_insertq('spip_gis_liens',
			array('id_gis' => $id_gis,
				'id_objet' => $id_objet,
				'objet' => $objet));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_gis/$id_gis'");
		return true;
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
		sql_delete('spip_gis_liens', "id_gis=$id_gis AND id_objet=$id_objet AND objet=". sql_quote($objet));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_gis/$id_gis'");
		return true;
	}
	return false;
}

/**
 * Supprimer définitivement un point géolocalisé
 * 
 * @param int $id_gis identifiant numérique du point
 * @return 0/false 0 si réussite, false dans le cas ou le point n'existe pas
 */
function supprimer_gis($id_gis){
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

?>
