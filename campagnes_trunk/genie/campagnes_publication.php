<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Vérifie si les pubs inactives doivent être publiées
 * ou si les pubs publiées doivent être obsolètes
 * suivant les dates s'il y en a
 */
function genie_campagnes_publication_dist($time){
	include_spip('base/abstract_sql');
	$jourdhui = date('Y-m-d');
	
	// On regarde les publicités inactives qui ont des dates de début <= à aujourd'hui
	if ($a_changer = sql_allfetsel(
		'id_campagne',
		'spip_campagnes',
		array(
			'statut = '.sql_quote('prepa'),
			'date_debut != "0000-00-00"',
			'date_fin != "0000-00-00"',
			'date_debut <= '.sql_quote($jourdhui),
			'date_fin >= '.sql_quote($jourdhui)
		)
	) and is_array($a_changer)){
		$a_changer = array_map('reset', $a_changer);
		sql_updateq(
			'spip_campagnes',
			array('statut' => 'publie'),
			sql_in('id_campagne', $a_changer)
		);
	}
	
	// On regarde les publicités publiées qui ont des dates de fin < à aujourd'hui
	if ($a_changer = sql_allfetsel(
		'id_campagne',
		'spip_campagnes',
		array(
			'statut = '.sql_quote('publie'),
			'date_fin != "0000-00-00"',
			'date_fin < '.sql_quote($jourdhui)
		)
	) and is_array($a_changer)){
		$a_changer = array_map('reset', $a_changer);
		sql_updateq(
			'spip_campagnes',
			array('statut' => 'obsolete'),
			sql_in('id_campagne', $a_changer)
		);
	}
	
	return 1;
}

?>
