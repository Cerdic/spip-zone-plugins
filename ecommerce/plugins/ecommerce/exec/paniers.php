<?php

include_spip("inc/presentation");
include_spip('inc/ecommerce_outils');
include_spip("inc/ecommerce_panier");


function exec_paniers_dist()
	{
	//global $clean_link;
	include_spip("inc/presentation");
	boutique_verifier_base();

	global $connect_statut;

	debut_page(_T('Panier:Panier'));
	if ($connect_statut == "0minirezo") 
		{
		debut_gauche();
		debut_boite_info();
		echo _L("Cliquez sur un paniers pour le visualiser avant suppression.");
		fin_boite_info();
		debut_droite();
		afficher_paniers (_L("Tous les paniers"),
			array(
				"SELECT" => "id_session",
				"FROM" => "spip_ecommerce_paniers",
				"GROUP BY" => "id_session",
				"ORDER BY" => "id_session DESC")
				);
		echo "<br />\n";	
		}
	else 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
	fin_page();
	}
?>

