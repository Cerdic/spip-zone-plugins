<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function contact_infos_supplementaires() {
	$champs = array(
		'civilite' => _T('contact:champ_civilite'),
		'prenom' => _T('contact:champ_prenom'),
		'nom' => _T('contact:champ_nom'),
		'organisation' => _T('contact:champ_organisation'),
		'adresse' => _T('contact:champ_adresse'),
		'code_postal' => _T('contact:champ_code_postal'),
		'ville' => _T('contact:champ_ville'),
		'etat' => _T('contact:champ_etat'),
		'pays' => _T('contact:champ_pays'),
		'telephone' => _T('contact:champ_telephone'),
		'portable' => _T('contact:champ_portable'),
		'mail' => _T('contact:champ_mail'),
		'sujet' => _T('contact:champ_sujet'),
		'texte' => _T('contact:champ_texte'),
		'infolettre' => _T('contact:champ_infolettre')
	);

	$champs = pipeline('contact_infos_supplementaires', $champs);
	return $champs;
}
