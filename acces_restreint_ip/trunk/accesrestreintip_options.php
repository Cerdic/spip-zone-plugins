<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

if (isset($GLOBALS['meta']["accesrestreint_base_version"])){
	// Si on n'est pas connecte, on ne passe pas par le pipeline
	// alors on ajoute directement les zones par IP
	if (!isset($GLOBALS['visiteur_session']['id_auteur'])) {
		$GLOBALS['accesrestreint_zones_autorisees'] .= accesrestreintip_lister_zones_par_ip();
		
		// Puisqu'on passe après Accès Restreint, obligé d'ajouter nous-même le marqueur
		if (!isset($GLOBALS['marqueur'])) {
			$GLOBALS['marqueur'] = '';
		}
		$GLOBALS['marqueur'] .=
			':accesrestreintip_zones_autorisees='
			.$GLOBALS['accesrestreint_zones_autorisees'];
	}
}

function accesrestreintip_lister_zones_par_ip($ip=null) {
	include_spip('base/abstract_sql');
	$zones_autorisees = array();
	
	// Récupération IP du client si pas en argument
	if (is_null($ip) and isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	$long_ip = ip2long($ip);
	
	if ($zones = sql_allfetsel('id_zone, ips', 'spip_zones', 'ips != ""') and is_array($zones)) {
		foreach ($zones as $zone) {
			$ranges = explode(',', $zone['ips']);
			foreach ($ranges as $range) {
				// Range d'IP contenant - comme séparateur
				if (preg_match ("/-/",$range))  {
					$ranges_2 = explode ('-', $range) ;
					$low_long_ip = ip2long($ranges_2[0]);
					$high_long_ip = ip2long($ranges_2[1]);
					if ($long_ip <= $high_long_ip and $low_long_ip <= $long_ip) {
						$zones_autorisees[] = $zone['id_zone'];
						break; // on a trouvé une IP bonne on ne continue pas plus loin
					}
				} // IP individuelle
				else {
					if ($long_ip == ip2long($range)) {
						$zones_autorisees[] = $zone['id_zone'];
						break; // on a trouvé une IP bonne on ne continue pas plus loin
					}
				}
			}
		}
	}
	
	return join(',', $zones_autorisees);
}
