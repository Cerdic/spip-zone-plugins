<?php
/*
 * Plugin Intranet
 * 
 * (c) 2013 kent1
 * Distribue sous licence GPL
 *
 */

/**
 * Autoriser a voir le site en Intranet : par defaut toute personne identifiée
 * @return mixed
 */
function autoriser_intranet_dist()
{
    $autoriser = false;
    // On laisse en premier le cas du user connecté, cas le plus courant pour l'utilisation de ce plugin
    if (isset($GLOBALS['visiteur_session']['id_auteur']) &&    $GLOBALS['visiteur_session']['id_auteur'] > 0    )
    {
        $autoriser = true;
    } else {
        /*cas intranet definit par une IP ou un range d'ip ...à faire : compatibilité IPV6 */
        if (!function_exists('lire_config'))
            include_spip('inc/config');

        //récupération ip du client
        if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
        $long_ip = ip2long($ip);

        $ranges = explode(',', lire_config('intranet/plageip', ' '));
        foreach ($ranges as $range) {
            //Range d'ip contenant - comme séparateur
            if (preg_match ("/-/",$range))  {
                $ranges_2 = explode ('-', $range) ;
                $low_long_ip = ip2long($ranges_2[0]);
                $high_long_ip = ip2long($ranges_2[1]);
                if ($long_ip <= $high_long_ip && $low_long_ip <= $long_ip) {
                    $autoriser = true;
                    break; // on a trouvé une ip bonne on ne continue pas plus loin
                }
            } // Ip individuelle
            else {
                if ($long_ip == ip2long($range)) {
                    $autoriser = true;
                    break;
                }
            }
        }
    }
    return $autoriser;
}

// dans le site public
// si auteur pas autorise : placer sur un cache dedie
if (!test_espace_prive()){
	include_spip('inc/autoriser');
	if (!autoriser('intranet'))
		$GLOBALS['marqueur'].= ":intranet_out";
}

/**
 * Pipeline styliser pour rerouter tous les fonds vers intranet
 *
 * @param array $flux
 * @return array
 */
function intranet_styliser($flux){
	if ( 
		!test_espace_prive()
		AND strpos($flux['args']['fond'],'/')===false
		AND !in_array(substr($flux['args']['fond'],-3),array('.js','.css'))
		AND include_spip('inc/autoriser')
		AND !autoriser('intranet')
		AND include_spip('inc/config')
		AND ($pages_ok = array_filter(pipeline('intranet_pages_ok',array_merge(array('robots.txt','spip_pass','favicon.ico','informer_auteur'),explode(',',lire_config('intranet/pages_intranet',' '))))))
		AND !in_array($flux['args']['fond'],$pages_ok)
		AND !in_array($flux['args']['contexte'][_SPIP_PAGE],$pages_ok)){
			$fond = trouver_fond('inclure/intranet','',true);
			$flux['data'] = $fond['fond'];
	}
	return $flux;
}

?>