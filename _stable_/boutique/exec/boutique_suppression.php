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

//
// DEBUGGING MODE
//
//	echo "<p><strong>"._L("UPDATE [resultat -> $result]")."</strong> ";
//	exit;
//
// FIN
//	


include_spip('inc/ecommerce_boutique');
include_spip("inc/presentation");
include_spip("inc/config");




function exec_boutique_suppression()
	{
	global $spip_lang_right;

	//
	// Affichage de la page
	//
	debut_page("&laquo; $titre &raquo;", "documents", "Boutique","");
	debut_gauche();
	debut_droite();


	if ($retour)
		$retour = urldecode($retour);


	$result_delete = spip_query("DROP TABLE `spip_ecommerce_sessions`; ");
	$num_rows_delete = spip_num_rows($result_delete);
//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("DELETE [resultat -> $result_delete, $num_rows_delete]")."</strong> ";
//	exit;
//
// FIN
//	
	$result_delete = spip_query("DROP TABLE `spip_ecommerce_paniers`; ");
	$num_rows_delete = spip_num_rows($result_delete);
//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("DELETE [resultat -> $result_delete, $num_rows_delete]")."</strong> ";
//	exit;
//
// FIN
//	





//
// Icones retour et suppression
//
	echo "<div style='text-align:$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "rien.gif",'right');
	$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('boutique')));
	echo "<div style='clear:both;'></div>";
	echo "</div>";
//
// FIN
//
	fin_page();
	}
?>
