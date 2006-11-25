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

