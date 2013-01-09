<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_amap_panier_charger_dist($id_amap_panier='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('amap_panier', $id_amap_panier, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_amap_panier_verifier_dist($id_amap_panier='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('amap_panier', $id_amap_panier, array('id_auteur', 'id_producteur', 'date_distribution'));
	return $erreurs;
}

function formulaires_editer_amap_panier_traiter_dist($id_amap_panier='new', $retour=''){
	return formulaires_editer_objet_traiter('amap_panier', $id_amap_panier, '', '', $retour, '');
}
?>
