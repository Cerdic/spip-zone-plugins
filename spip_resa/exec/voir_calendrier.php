<?php

require_once dirname(__FILE__) . '/../inc/Calendrier.php' ;

function exec_voir_calendrier()
{
    $commencer_page = charger_fonction('commencer_page', 'inc') ;
    echo $commencer_page(_T('resa:ajouter_calendrier_reservation')) ;
   
    echo debut_gauche('', true) ;
	echo recuperer_fond('prive/menu') ;

    echo debut_droite('', true) ;
	
	$idCal = (int) _request('id_calendrier') ;
	if( $idCal !== 0 )
	{
		resa_afficher_calendrier($idCal, true) ;
	} else {
		debut_boite_alerte() ;
		echo '<p>' . _T('resa:msg_identifiant_manquant') . '</p>' ;
		fin_boite_alerte() ;
	}

	echo fin_gauche() ;
    echo fin_page() ;
}
