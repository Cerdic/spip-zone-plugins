<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_player_admin()
{
	global $connect_statut,$connect_toutes_rubriques;
	
	include_spip('inc/presentation');
	include_spip('inc/meta');
	include_spip('inc/config');

	include_spip('player_config_fonctions');

	debut_page("Lecteur multimedia");

	debut_gauche();
	
	creer_colonne_droite();
	
	
	debut_droite("Lecteur multimedia");
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	echo player_affiche_config_form('config_fonctions');

	fin_page();
}

?>