<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

function formulaires_affecter_zones_charger_dist($id_auteur){
	$valeurs = array('zone'=>'','id_auteur'=>$id_auteur,'id'=>$id_auteur);
	include_spip('inc/autoriser');
	if (!autoriser('affecterzones','auteur',$id_auteur)){
		$valeurs['editable'] = false;
	}
	return $valeurs;
}

function formulaires_affecter_zones_traiter_dist($id_auteur){
	/* ajout d'une zone */
	include_spip('action/editer_zone');
	accesrestreint_revision_zone_objets_lies(intval(_request('zone')),$id_auteur,'auteur');
	return array('editable'=>true,'message'=>'');
}
