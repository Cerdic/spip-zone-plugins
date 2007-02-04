<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/mise_a_jour');
include_spip('inc/distant');	//recuperer_page()
include_spip('inc/filtres');	//normaliser_date()

function action_verifier_spip_loader() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$redirect = urldecode(_request('redirect'));
	$resultat = null;
	$spip_loader = unserialize($GLOBALS['meta']['spip_loader']);

	//verifier
	if(@file_exists($f = _SPIP_LOADER_LOCAL_SCRIPT)){
		$spip_loader['date_script_local'] = date('Y-m-d H:i:s', filemtime($f));
		$date_verif = $spip_loader['date_verif'] > 0 ? $spip_loader['date_verif'] : 0 ;
		if($resultat = recuperer_page(
			_SPIP_LOADER_SOURCE_SCRIPT,
			false, true, 1048576, '', '', false,
			$date_verif
		)){
			$spip_loader['date_verif'] = date('Y-m-d H:i:s', time());
			if($resultat != '200' AND preg_match(',Last-Modified: (.*),', $resultat, $r)){
				$date_reference = normaliser_date($r[1]);				
			}
		}
	}
	if(isset($date_reference) AND $date_reference > 0) {
		$spip_loader['date_script_distant'] = $date_reference;
	}

	//stocker
	ecrire_meta('spip_loader', serialize($spip_loader), 'non');
	ecrire_metas();

	if($redirect)
		redirige_par_entete(parametre_url($redirect, 'verif', $resultat != null ? 'ok' : 'ko', '&'));
	exit;
}

?>