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

function formulaires_editer_amap_livraison_charger_dist($id_amap_livraison='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('amap_livraison', $id_amap_livraison, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_amap_livraison_verifier_dist($id_amap_livraison='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('amap_livraison', $id_amap_livraison, array('date_livraison', 'contenu_panier'));
	return $erreurs;
}

function formulaires_editer_amap_livraison_traiter_dist($id_amap_livraison='new', $retour=''){
	return formulaires_editer_objet_traiter('amap_livraison', $id_amap_livraison, '', '', $retour, '');
}

?>
