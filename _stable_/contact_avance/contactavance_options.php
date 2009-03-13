<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['spip_pipeline']['contact_infos_supplementaires'] = '';
function contactavance_infos_supplementaires(){
	$champs = array(
		'prenom' => _T('contactavance:champ_prenom'),
		'nom' => _T('contactavance:champ_nom'),
		'organisation' => _T('contactavance:champ_organisation'),
		'telephone' => _T('contactavance:champ_telephone'),
		'portable' => _T('contactavance:champ_portable'),
		'adresse' => _T('contactavance:champ_adresse'),
		'code_postal' => _T('contactavance:champ_code_postal'),
		'ville' => _T('contactavance:champ_ville'),
		'pays' => _T('contactavance:champ_pays')
	);
	
	$champs = pipeline('contact_infos_supplementaires', $champs);
	return $champs;
}

?>
