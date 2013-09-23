<?php
/**
* @plugin	Amap
* @author	Stephane Moulinet
* @author	E-cosystems
* @author	Pierre KUHN 
* @copyright 2010-2013
* @licence	GNU/GPL
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_amap_responsable_charger_dist($id_amap_responsable='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('amap_responsable', $id_amap_responsable, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_amap_responsable_verifier_dist($id_amap_responsable='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('amap_responsable', $id_amap_responsable, array('date_distribution', 'id_auteur'));
	return $erreurs;
}

function formulaires_editer_amap_responsable_traiter_dist($id_amap_responsable='new', $retour=''){
	return formulaires_editer_objet_traiter('amap_responsable', $id_amap_responsable, '', '', $retour, '');
}
?>
