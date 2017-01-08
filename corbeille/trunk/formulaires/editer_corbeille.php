<?php
/**
 * Plugin Corbeille 3.0
 * La corbeille pour Spip 3.0
 * Collectif
 * Licence GPL
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_corbeille_charger_dist($table){
	include_spip('action/corbeille_vider');
	$params = corbeille_table_infos($table);
	return array('table'=>$table,'elements'=>array(),'recherche'=>_request('recherche'),'_statut'=>$params['statut']);
}


function formulaires_editer_corbeille_traiter_dist($table){
	include_spip('action/corbeille_vider');
	include_spip('inc/objet');
	if(_request('effacer_tout'))
		$res = corbeille_vider($table,-1);
	else
		$res = corbeille_vider($table,_request('elements'));
	$objet_sg = objet_type($table);
	$objet_pl = table_objet($table);
	if ($res){
		if (count($res)>1)
			$message = _T('corbeille:elements_supprimes',array('objet'=>$objet_pl,'ids'=>'#'.join(', #',$res)));
		else
			$message = _T('corbeille:element_supprime',array('objet'=>$objet_sg,'ids'=>'#'.join(', #',$res)));
	}
	else
		$message = _T('corbeille:aucun_element_supprime');
	
	return array('message_ok'=>$message);
}
