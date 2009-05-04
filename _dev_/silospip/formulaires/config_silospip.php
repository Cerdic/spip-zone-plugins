<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin de création de sites en libre service                           *
 *                                                                         *
 *  Copyright (c) 2009                                                     *
 *  Daniel Viñar Ulriksen                                                  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

function formulaires_config_silospip_charger_dist(){

	$champs = array();
	$domaines = lire_config('silospip_domaines/');
	$champs['domaines'] = is_array($domaines) ? $domaines : array();
	$champs['prefere'] = lire_config('silospip_prefere/','1');
	$champs['url_panel'] = lire_config('silospip_url_panel/');
	$champs['admin_panel_user'] = lire_config('silospip_admin_panel_user/');
	$champs['admin_panel_pass'] = lire_config('silospip_admin_panel_pass/');
	
	return $champs;
}

function formulaires_config_silospip_verifier_dist(){

	return array();
}

function formulaires_config_silospip_traiter_dist(){

	$domaines = lire_config('silospip_domaines/');
	if(!is_array($domaines))
		$domaines = array();
	$modif = false;
	$efface = false;
	
	foreach($domaines as $cle => $dom) {
		if ($dom !== _request($cle)) {
			$modif = true;
			if (_request($cle) == '') {
				unset($domaines[$cle]);
				$efface = true;
			} else
				$domaines[$cle] = _request($cle);
		}
	}
	if ($efface) 
		$domaines = array_values($domaines);
	
	if (_request('nuvo_domaine')) {
		$domaines[] = _request('nuvo_domaine');
		if (_request('prefere') == 'nuvo') 
			ecrire_config('silospip_prefere', count($domaines));
	}

	if ($modif || _request('nuvo_domaine'))
		ecrire_config('silospip_domaines/', $domaines);
	if ( _request('prefere') !== 'nuvo')
		ecrire_config('silospip_prefere', _request('prefere'));
	if ( _request('url_panel'))
		ecrire_config('silospip_url_panel/', _request('url_panel'));
	if ( _request('admin_panel_user'))
		ecrire_config('silospip_admin_panel_user/', _request('admin_panel_user'));
	if ( _request('admin_panel_pass'))
		ecrire_config('silospip_admin_panel_pass/', _request('admin_panel_pass'));

	return array('message_ok' => 'C-est OK');
}

?>
