<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function formulaires_affectation_zones_charger_dist($id_auteur){
	$valeurs = array('zone'=>'','id_auteur'=>$id_auteur,'id'=>$id_auteur);
	if (!autoriser('affecterzones','auteur',$id_auteur)){
		$valeurs['editable'] = false;
	}
	return $valeurs;
}

function formulaires_affectation_zones_traiter_dist($id_auteur){
	/* ajout d'une zone */
	if ($id_zone = sql_getfetsel('id_zone','spip_zones','id_zone='.intval(_request('zone')))) {
		sql_insertq('spip_zones_auteurs',array('id_zone'=>$id_zone,'id_auteur'=>intval($id_auteur)));
	}
	return array('editable'=>true,'message'=>'');
}