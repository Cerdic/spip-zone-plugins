<?php

/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com

+--------------------------------------------+
| ecrire configuration                       |
+--------------------------------------------+
*/

session_start();

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function action_mercure_config() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/headers');
	
	
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	$metas=array();

//	$metas['first_use'] = _request('mercure_first_use');
	$metas['first_use'] = FALSE;

	$metas['menu'] = _request('mercure_menu');
	$metas['maj_connectes'] = _request('mercure_maj_connectes');

	$metas['bdd'] = _request('mercure_bdd');
	$metas['item_limit'] = _request('mercure_bdd_item_limit');
	$metas['time_limit'] = _request('mercure_bdd_time_limit');
	
	$metas['refresh'] = _request('mercure_refresh');
	$metas['nb_lignes'] = _request('mercure_nb_lignes');

  $metas['notify'] = _request('mercure_notify');
  if($metas['notify'] == 'on'){
    $_SESSION['mercure_notify_sound'] = 'on';
  }else{
    $_SESSION['mercure_notify_sound'] = 'off';
  }
  
  $metas['notify_sound'] = _request('mercure_notify_sound');
  $metas['notify_volume'] = _request('mercure_notify_volume');
  
	$metas['version'] = _request('version_plug');
	
	$chaine = serialize($metas);
	ecrire_meta('mercure',$chaine);
	ecrire_metas();
	
	$redirect = urldecode(_request('redirect'));
}
?>
