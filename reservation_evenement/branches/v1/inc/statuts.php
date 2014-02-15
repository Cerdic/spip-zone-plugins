<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_statuts_complet_dist(){
	include_spip('inc/config');
	$config=lire_config('reservation_evenement',array());
	$statuts=isset($config['statuts_complet'])?$config['statuts_complet']:'';
	if(!$statuts){
		$statut_defaut=isset($config['statut_defaut'])?$config['statut_defaut']:'rien'; 
		$statut=charger_fonction('defaut','inc/statut');
		$statut_defaut=$statut($statut_defaut);
		$statuts=array($statut_defaut);
		}	
	return $statuts;
}

function inc_statut_defaut_dist($statut=''){
	if(!$statut OR $statut=='rien'){
		include_spip('inc/config');
		if(!$statut=lire_config('reservation_evenement/statut_defaut'))$statut='attente';		
	}		
	return $statut;
}

?>
