<?php

function exec_ajouter_calendrier()
{
    $commencer_page = charger_fonction('commencer_page', 'inc') ;
    echo $commencer_page(_T('resa:ajouter_calendrier_reservation')) ;
   
    echo debut_gauche('', true) ;
	echo recuperer_fond('prive/menu') ;

    echo debut_droite('', true) ;
	
	echo recuperer_fond('prive/ajouter_calendrier') ;

	echo fin_gauche() ;
    echo fin_page() ;
}
