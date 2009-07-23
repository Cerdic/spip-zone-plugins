<?php

function resa_header_prive($flux)
{
	$exec = _request('exec') ;
	
	$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_RESA . 'css/resa.css" type="text/css" />'. "\n" ;
	
	if( $exec == 'voir_calendrier' ) {
		$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_RESA . 'css/calendrier.css" type="text/css" />'. "\n" ;
		$flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_RESA . 'scripts/calendrier.js"></script>'. "\n" ;
	} elseif( $exec == 'voir_calendriers' ) {
		$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_RESA . 'css/liste_calendriers.css" type="text/css" />' . "\n" ;
	}
	
	return $flux;
}

function resa_mois_wrapper($mois)
{
	$l = strlen(trim($mois)) ;
	$flag = false ;
	$tmp = '' ;
	$new_mois = '' ;
	for( $i = 0 ; $i < $l ; $i++ )
	{
		if( $mois[$i] == '&' ) $flag = true ;
		if( !$flag )
			$new_mois .= '<span>' . utf8_encode($mois[$i]) . '</span>' ;
		else
			$tmp .= $mois[$i] ;
		if( $mois[$i] == ';' ) {
			$flag = false ;
			$new_mois .= '<span>' . $tmp . '</span>' ;
		}
	}
	
	return $new_mois ;
}

function resa_afficher_calendrier($idCal, $admin=false)
{
	require_once _DIR_PLUGIN_RESA . 'inc/Calendrier.php' ;
	
	$resCal = sql_select('ts', 'spip_resa_reservation', 'id_calendrier=' . sql_quote($idCal)) ;
	if( !$resCal ) return ;

	global $joursReserves ;
			
	$joursReserves = array() ;
	while( $resa = sql_fetch($resCal) )
		$joursReserves[] = (int) $resa['ts'] ;
		
	if( sql_getfetsel('id_calendrier', 'spip_resa_calendrier', 'id_calendrier=' . sql_quote($idCal)) === null )
		return ;
		
	try
	{
		$cal = new Calendrier($idCal, lire_config('resa/mois_depart'), 0, lire_config('resa/nb_mois')) ;
		
		Mois::$moisFr = array(1 => _T('resa:janvier'), _T('resa:fevrier'), _T('resa:mars'), _T('resa:avril'), _T('resa:mai'), _T('resa:juin'), _T('resa:juillet'), _T('resa:aout'), _T('resa:septembre'), _T('resa:octobre'), _T('resa:novembre'), _T('resa:decembre')) ;
		
		if( lire_config('resa/type_affichage_mois') == 'vertical' )
		{
			Mois::$moisFr = array_map('resa_mois_wrapper', Mois::$moisFr) ;
		}
		
		Jour::$joursFr = array(1 => _T('resa:lundi'), _T('resa:mardi'), _T('resa:mercredi'), _T('resa:jeudi'), _T('resa:vendredi'), _T('resa:samedi'), _T('resa:dimanche')) ;
		
		// Le code commenté nécessite PHP >= 5.3 (closures)
		$cal -> setCallbackReserve(
			create_function('$ts', 'global $joursReserves ; return in_array((int) $ts, $joursReserves) ;')
			/*function($ts) {
				global $joursReserves ;
				return in_array($ts, $joursReserves) ;
			}*/
		) ;
		$func =
			$admin ?
				create_function('$idCal,$ts', 'return \'<a href="#" onclick="reserverJour(\' . $idCal . \', \' . $ts . \');">\' . date(\'d\', $ts) . \'</a>\' ;')
				/*function($idCal, $ts) {
					return '<a href="#" onclick="reserverJour(' . $idCal . ', ' . $ts . ');">' . date('d', $ts) . '</a>' ;
				}*/
			:
				create_function('$idCal,$ts', 'return date(\'d\', $ts) ;') ;
				/*function($idCal, $ts) {
					return date('d', $ts) ;
				}*/
		;
		
		$cal -> setCallbackFormat($func) ;
		echo $cal ;	
	} catch (Exception $e) {
		debut_boite_alerte() ;
		echo $e -> getMessage() ;
		fin_boite_alerte() ;
	}
}

function resa_supprimer_calendrier_article($idArticle, $idCalendrier)
{
	$texte = sql_getfetsel('texte', 'spip_articles', 'id_article=' . sql_quote((int) $idArticle)) ;
	sql_updateq(
		'spip_articles',
		array('texte' => str_replace('<calendrier1|id_calendrier=' . $idCalendrier . '>', '', $texte)),
		'id_article=' . sql_quote((int) $idArticle)
	) ;
}
