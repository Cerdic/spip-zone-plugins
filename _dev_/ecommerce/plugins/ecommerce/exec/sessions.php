<?php

include_spip("inc/presentation");
include_spip("inc/ecommerce_session");
include_spip("inc/ecommerce_outils");

function exec_sessions_dist()
	{
	//global $clean_link;
	include_spip("inc/presentation");
	boutique_verifier_base();

	global $connect_statut;

	debut_page(_T('Panier:Panier'));
	if ($connect_statut == "0minirezo") 
		{
		debut_droite();
		debut_gauche();
		debut_boite_info();
		echo _L("Cliquez sur une session pour la visualiser avant suppression.");
		fin_boite_info();
		debut_droite();
		afficher_sessions (_L("Toutes les sessions"),
			array(
				"FROM" => "spip_ecommerce_sessions",
				"GROUP BY" => "id_session",
				"ORDER BY" => "maj DESC")
				);
		echo "<br />\n";
		}
	else 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
	fin_page();
	}
?>

