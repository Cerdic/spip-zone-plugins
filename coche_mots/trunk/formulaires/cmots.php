<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

function cmots_mots_deja_associes($id_groupe, $objet, $id_objet) {
	$select_id_mots = sql_allfetsel(
		'mots.id_mot AS id_mot', // select
		array('spip_mots AS mots','spip_mots_liens AS liens'), // from
		array('mots.id_groupe='.intval($id_groupe),'liens.objet='.sql_quote($objet),'liens.id_objet='.intval($id_objet),'mots.id_mot=liens.id_mot') // where
	);
	$mots=array();
	foreach ($select_id_mots as $select_id_mot)
		$mots[] = $select_id_mot['id_mot'];
	return $mots;
}

function formulaires_cmots_charger_dist($id_groupe, $objet, $id_objet, $retour=''){
	$contexte = array();
	$contexte['id_groupe'] = $id_groupe;
	$contexte['objet'] = $objet;
	$contexte['id_objet'] = $id_objet;
	$contexte['mots'] = cmots_mots_deja_associes($id_groupe, $objet, $id_objet);
	return $contexte;
}

function formulaires_cmots_traiter_dist($id_groupe, $objet, $id_objet, $retour=''){
	$mots_en_base = cmots_mots_deja_associes($id_groupe, $objet, $id_objet);
	$mots_demandes = _request('mots');
	if (!$mots_demandes) $mots_demandes = array();
	$mots_a_associer = array_diff($mots_demandes,$mots_en_base);
	$mots_a_dissocier = array_diff($mots_en_base,$mots_demandes);
	
	include_spip('action/editer_liens');
	if (count($mots_a_associer)>0) objet_associer(array('mot' => $mots_a_associer),array($objet => $id_objet));
	if (count($mots_a_dissocier)>0) objet_dissocier(array('mot' => $mots_a_dissocier),array($objet => $id_objet));
	
	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");
	$res['message_ok'] = _T('info_modification_enregistree');
	if ($retour) {
		if (strncmp($retour,'javascript:',11)==0){
			$res['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retour,11).'/*]]>*/</script>';
			$res['editable'] = true;
		}
		else
			$res['redirect'] = $retour;
	}
	return $res;
}
