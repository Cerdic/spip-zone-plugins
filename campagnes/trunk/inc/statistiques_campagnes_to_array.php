<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_statistiques_campagnes_to_array_dist($type, $id, $date_debut='', $date_fin='') {
	$id_campagne = intval($id);
	$where = array();
	
	// On vérifie qu'on a les bons paramètres
	if (!in_array($type, array('campagne', 'annonceur')) or !$id_campagne>0){ return array(); }
	
	// On cherche quelle(s) campagne(s) on doit sortir
	if ($type == 'campagne'){
		$where[] = 'id_campagne = '.$id;
		$group = 'date';
	}
	elseif($type == 'annonceur'){
		$ids_campagnes = sql_allfetsel('id_campagne', 'spip_campagnes', 'id_annonceur = '.$id);
		if (!$ids_campagnes){ $ids_campagnes = array(); }
		$ids_campagnes = array_map('reset', $ids_campagnes);
		$where[] = sql_in('id_campagne', $ids_campagnes);
		$group = 'id_campagne, date';
	}
	
	// S'il n'y a aucune date on sort juste le mois en cours
	if (!$date_debut and !$date_fin){
		$date_debut = date('Y-m-01');
		$date_fin = date('Y-m-d');
	}
	elseif (!$date_debut){
		$date_debut = '0000-00-00';
	}
	elseif (!$date_fin){
		$date_fin = '3000-00-00';
	}
	$where[] = 'date >= '.sql_quote($date_debut);
	$where[] = 'date <= '.sql_quote($date_fin);
	
	$vues = sql_allfetsel(
		'id_campagne, date, count(cookie) as vues, 0 as clics',
		'spip_campagnes_vues',
		$where,
		$group,
		'date'
	);
	
	$clics = sql_allfetsel(
		'id_campagne, date, count(cookie) as clics',
		'spip_campagnes_clics',
		$where,
		$group,
		'date'
	);
	
	foreach ($vues as $cle_vue => $date_vues){
		foreach ($clics as $date_clics){
			if ($date_vues['date'] == $date_clics['date']){
				$vues[$cle_vue]['clics'] = $date_clics['clics'];
			}
		}
		if ($vues[$cle_vue]['vues'] > 0){
			$vues[$cle_vue]['ratio'] = round($vues[$cle_vue]['clics'] / $vues[$cle_vue]['vues'] * 100, 2);
		}
		else{
			$vues[$cle_vue]['ratio'] = 0;
		}
	}
	return $vues;
}

?>
