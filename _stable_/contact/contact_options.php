<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['spip_pipeline']['contact_infos_supplementaires'] = '';
function contact_infos_supplementaires(){
	$champs = array(
		'prenom' => _T('contact:champ_prenom'),
		'nom' => _T('contact:champ_nom'),
		'organisation' => _T('contact:champ_organisation'),
		'telephone' => _T('contact:champ_telephone'),
		'portable' => _T('contact:champ_portable'),
		'adresse' => _T('contact:champ_adresse'),
		'code_postal' => _T('contact:champ_code_postal'),
		'ville' => _T('contact:champ_ville'),
		'pays' => _T('contact:champ_pays')
	);
	
	$champs = pipeline('contact_infos_supplementaires', $champs);
	return $champs;
}

?>
