<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_gis_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	// si id_gis n'est pas un nombre, c'est une creation
	if (!$id_gis = intval($arg)) {
		if (!$id_gis = insert_gis())
			return array(false,_L('echec'));
	}
	$err = revisions_gis($id_gis);
	return array($id_gis,$err);
}

function insert_gis() {
	$champs = array();
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_gis',
		),
		'data' => $champs
	));
	
	$id_gis = sql_insertq("spip_gis", $champs);
	return $id_gis;
}

// Enregistrer certaines modifications d'un gis
function revisions_gis($id_gis, $c=false) {
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
	
	if(intval(_request('id_objet')) && _request('objet'))
		lier_gis($id_gis, _request('objet'), _request('id_objet'));
}

/**
 * Lier un point géolocalisé à un objet SPIP
 * 
 * @param int $id_gis identifiant numérique du point
 * @param string $objet Le type de l'objet à lier
 * @param int $id_objet L'identifiant numérique de l'objet lié
 */
function lier_gis($id_gis, $objet, $id_objet){
	//$objet = objet_type($objet);
	if ($id_objet AND $id_gis
	AND preg_match('/^[a-z0-9_]+$/i', $objet) # securite
	AND !sql_getfetsel("id_gis", "spip_gis_liens", "id_gis=$id_gis AND id_objet=$id_objet AND objet=".sql_quote($objet))
	) {
		sql_insertq('spip_gis_liens',
			array('id_gis' => $id_gis,
				'id_objet' => $id_objet,
				'objet' => $objet));
	}
}

/**
 * Délier un point géolocalisé d'un objet SPIP
 * 
 * @param int $id_gis identifiant numérique du point
 * @param string $objet Le type de l'objet à lier
 * @param int $id_objet L'identifiant numérique de l'objet lié
 */
function delier_gis($id_gis, $objet, $id_objet){
	//$objet = objet_type($objet);
	if ($id_objet AND $id_gis
	AND preg_match('/^[a-z0-9_]+$/i', $objet) # securite
	) {
		sql_delete('spip_gis_liens', "id_gis=$id_gis AND id_objet=$id_objet AND objet=". sql_quote($objet));
	}
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_gis/$id_gis'");
}

function supprimer_gis($id_gis){
	if (intval($id_gis)){
		sql_delete("spip_gis_liens", "id_gis=".intval($id_gis));
		sql_delete("spip_gis", "id_gis=".intval($id_gis));
	}
	$id_gis = 0;
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_gis/$id_gis'");
	return $id_gis;
}

?>