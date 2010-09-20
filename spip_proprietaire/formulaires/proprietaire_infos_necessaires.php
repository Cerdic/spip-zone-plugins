<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_proprietaire_infos_necessaires_charger_dist($who='proprietaire'){
	$conf = spip_proprio_recuperer_config();
	$valeurs = array(
		'nom' => $conf[$who.'_nom'] ? $conf[$who.'_nom'] : $GLOBALS['meta']['nom_site'],
		'libelle' => $conf[$who.'_libelle'],
		'mail' => $conf[$who.'_mail'] ? $conf[$who.'_mail'] : $GLOBALS['meta']['email_webmaster'],
		'nom_responsable' => $conf[$who.'_nom_responsable'],
		'fonction_responsable' => $conf[$who.'_fonction_responsable'],
		'mail_responsable' => $conf[$who.'_mail_responsable'],
		'mail_administratif' => $conf[$who.'_mail_administratif'] ? $conf[$who.'_mail_administratif'] : $GLOBALS['meta']['email_webmaster'],
		'site_web' => $conf[$who.'_site_web'] ? $conf[$who.'_site_web'] : 'http://',
		'who' => $who,
	);
	return $valeurs;
}

function formulaires_proprietaire_infos_necessaires_verifier_dist($who='proprietaire'){
	$erreurs = array();
	include_spip('inc/filtres');
	if(!$nom = _request('nom')) 
		$erreurs['nom'] = _T('info_obligatoire');
	if(!$mail = _request('mail')) 
		$erreurs['mail'] = _T('info_obligatoire');
	elseif(!email_valide($mail))
		$erreurs['mail'] = _T('form_prop_indiquer_email');
	if(!$chef = _request('nom_responsable') AND $who=='proprietaire')
		$erreurs['nom_responsable'] = _T('info_obligatoire');
	if($chefmail = _request('mail_responsable') AND !email_valide($chefmail))
		$erreurs['mail_responsable'] = _T('form_prop_indiquer_email');
	if(!$adminmail = _request('mail_administratif') AND $who=='proprietaire')
		$erreurs['mail_administratif'] = _T('info_obligatoire');
	elseif($adminmail AND strlen($adminmail) AND !email_valide($adminmail))
		$erreurs['mail_administratif'] = _T('form_prop_indiquer_email');
	var_export($erreurs);
	return $erreurs;
}

function formulaires_proprietaire_infos_necessaires_traiter_dist($who='proprietaire'){
	$datas = array(
		$who.'_nom' => _request('nom'),
		$who.'_libelle' => _request('libelle'),
		$who.'_mail' => _request('mail'),
		$who.'_nom_responsable' => _request('nom_responsable'),
		$who.'_fonction_responsable' => _request('fonction_responsable'),
		$who.'_mail_responsable' => _request('mail_responsable'),
		$who.'_mail_administratif' => _request('mail_administratif'),
		$who.'_site_web' => _request('site_web'),
	);
	if( $ok = spip_proprio_enregistrer_config($datas) )
		return array('message_ok' => _T('spip_proprio:ok_config'));
	return array('message_erreur' => _T('spip_proprio:erreur_config'));
}
?>