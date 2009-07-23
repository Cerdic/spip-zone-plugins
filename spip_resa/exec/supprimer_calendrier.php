<?php

function exec_supprimer_calendrier()
{
    $commencer_page = charger_fonction('commencer_page', 'inc') ;
    echo $commencer_page(_T('resa:supprimer_calendrier_reservation')) ;
   
    echo debut_gauche('', true) ;
	echo recuperer_fond('prive/menu') ;

    echo debut_droite('', true) ;
	
	$idCal = (int) _request('id_calendrier') ;
	$idArt = (int) _request('id_article') ;
	if( $idCal !== 0 && $idArt !== 0 )
	{
		sql_delete(
			'spip_resa_reservation',
			'id_calendrier=' . sql_quote($idCal)
		) ;
		sql_delete(
			'spip_resa_calendrier',
			'id_calendrier=' . sql_quote($idCal)
		) ;
		
		resa_supprimer_calendrier_article($idArt, $idCal) ;
		debut_cadre_relief() ;
		echo '<p>' . _T('resa:msg_calendrier_supprime') . '</p>' ;
		fin_cadre_relief() ;
	} else {
		debut_boite_alerte() ;
		echo '<p>' . _T('resa:msg_identifiant_manquant') . '</p>' ;
		fin_boite_alerte() ;
	}

	echo fin_gauche() ;
    echo fin_page() ;
}
