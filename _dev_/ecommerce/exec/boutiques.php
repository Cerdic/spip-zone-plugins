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
include_spip("inc/ecommerce_boutique");

//
// boutique
//

function exec_boutiques_dist()
	{
	global $connect_statut;

	debut_page(_T('boutique:boutique'));
	if ($connect_statut == "0minirezo") 
		{
		if (estceque_boutique_editable()) 
			{
			debut_droite();
			debut_gauche();
			debut_boite_info();
			echo _L("Maintenance de la boutique en ligne.");
			fin_boite_info();
			debut_droite();
			echo "<div style='float:$spip_lang_left'>";
				$link=generer_url_ecrire('boutique_creation');
				$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
				icone(_L("Cr&eacute;er les tables de donnees"), $link, "../"._DIR_PLUGIN_BOUTIQUE. "/img_pack/euro.png", "creer.gif");
			echo "</div>";
			echo "<div style='float:$spip_lang_left'>";
				$link=generer_url_ecrire('boutique_suppression');
				$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
				icone(_L("Suppression des tables"), $link, "../"._DIR_PLUGIN_BOUTIQUE. "/img_pack/euro.png", "supprimer.gif");
			echo "</div>";
			}
		else
			echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
		}
	else 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
	fin_page();
	}
?>
