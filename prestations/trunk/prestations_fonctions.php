<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function prestations_calculer_quantite($id_prestation, $prestation=null) {
	static $quantites = array();
	$id_prestation = intval($id_prestation);
	if (!$prestation) {
		$prestation = sql_fetsel('*', 'spip_prestations', 'id_prestation = '.$id_prestation);
	}
	
	if (!isset($quantites[$id_prestation])) {
		$quantites[$id_prestation] = 0;
		
		if ($prestation['quantite']) {
			$quantites[$id_prestation] = floatval($prestation['quantite']);
		}
		elseif ($quantite_relative = $prestation['quantite_relative']) {
			$where = array(
				'objet = '.sql_quote($prestation['objet']),
				'id_objet = '.intval($prestation['id_objet']),
				'id_prestation != '.$id_prestation,
			);
			
			// Si du même type uniquement
			if ($prestation['quantite_relative_type']) {
				$where[] = 'id_prestations_type = '.intval($prestation['id_prestations_type']);
			}
			
			// Si rangs précédents uniquement
			if ($prestation['quantite_relative_rang']) {
				$where[] = 'rang < '.intval($prestation['rang']);
			}
			
			// On va chercher toutes les autres prestations avec ces conditions
			if ($autres_prestations = sql_allfetsel('id_prestation', 'spip_prestations', $where)) {
				$autres_prestations = array_map('reset', $autres_prestations);
				$autres_prestations = array_map('prestations_calculer_quantite', $autres_prestations);
				$quantite_totale = array_sum($autres_prestations);
				
				$quantites[$id_prestation] = $quantite_totale * $quantite_relative;
			}
		}
	}
	
	return $quantites[$id_prestation];
}
