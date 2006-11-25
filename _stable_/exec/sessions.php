<?php

/***************************************************************************
 *  BOUTIQUE : Plugin, version lite d'un e-commerce pour SPIP              *
 *                                                                         *
 *  Copyright (c) 2006-2007                                                *
 *  Laurent RIEFFEL : mailto:laurent.rieffel@laposte.net			   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 ***************************************************************************/

/*
 * Boutique
 * version plug-in d'un e-commerce
 *
 * Auteur : Laurent RIEFFEL
 * 
 * Module pour SPIP version 1.9.x
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

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

