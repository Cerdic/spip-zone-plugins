<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['spip_pipeline']['champs_contact_infos'] = '';
$GLOBALS['spip_pipeline']['champs_cotation_infos'] = '';

// Détails vesiteur
function champs_contact_infos(){
	$champs = array(

		'societe' => _T('cotation:champ_societe'),
		'nom' => _T('cotation:champ_nom'),
		'adresse' => _T('cotation:champ_adresse'),
		'telephone' => _T('cotation:champ_tel'),
		'fax' => _T('cotation:champ_fax'),
		'code_postal' => _T('cotation:champ_code_postal'),
		'ville' => _T('cotation:champ_ville'),
		'etat' => _T('cotation:champ_etat'),
		'pays' => _T('cotation:champ_pays')
	);
	
	$champs = pipeline('champs_contact_infos', $champs);
	return $champs;
}

//Détails cotation // cotation details array contenant les information propre a la cotation
function champs_cotation_infos(){
	$champs = array(
		'nature_marchandises' => _T('cotation:champ_nature_marchandises'),
		'poids_total' => _T('cotation:champ_poids_total'),
		'volume_total' => _T('cotation:champ_volume_total'),
		'lieu_depart' => _T('cotation:champ_lieu_depart'),
		'lieu_destination' => _T('cotation:champ_lieu_destination'),
		'quantite_marchandise' => _T('cotation:champ_quantite_marchandise')
	);
	
	$champs = pipeline('champs_cotation_infos', $champs);
	return $champs;
}

?>
