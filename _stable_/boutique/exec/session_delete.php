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




function exec_session_delete()
	{
	global $spip_lang_right;

	$new = _request('new');
	$retour = _request('retour');
	$id_session = _request('id_session');
	$supp_session = _request('supp_session');


	//
	// Affichage de la page
	//
	debut_page("&laquo; $titre &raquo;", "documents", "Boutique","");
	debut_gauche();
	debut_droite();

	if ($retour)
		$retour = urldecode($retour);

	if ($supp_session)
		{
		$result_delete = spip_query("SELECT id_session FROM spip_ecommerce_sessions WHERE statut='open' and ADDDATE(maj, INTERVAL 1 DAY) < now()");
		$num_rows_delete = spip_num_rows($result_delete);
		while ($row_delete = spip_fetch_array($result_delete)) 
			{
			$supp_session = $row['id_session'];
			$result = spip_query("DELETE FROM `spip_ecommerce_paniers` WHERE `id_session` = ".$supp_session);
			$result = spip_query("DELETE FROM `spip_ecommerce_sessions` WHERE `id_session` = ".$supp_session);
			}
		$result_delete = spip_query("SELECT id_session FROM spip_ecommerce_sessions WHERE statut='create' and ADDDATE(maj, INTERVAL 1 DAY) < now()");
		$num_rows_delete = spip_num_rows($result_delete);
		while ($row_delete = spip_fetch_array($result_delete)) 
			{
			$supp_session = $row['id_session'];
			$result = spip_query("DELETE FROM `spip_ecommerce_paniers` WHERE `id_session` = ".$supp_session);
			$result = spip_query("DELETE FROM `spip_ecommerce_sessions ` WHERE `id_session` = ".$supp_session);
			}
		$result_delete = spip_query("SELECT id_session FROM spip_ecommerce_sessoins WHERE statut='cancel' and ADDDATE(maj, INTERVAL 1 DAY) < now()");
		$num_rows_delete = spip_num_rows($result_delete);
		while ($row_delete = spip_fetch_array($result_delete)) 
			{
			$supp_session = $row['id_session'];
			$result = spip_query("DELETE FROM `spip_ecommerce_paniers` WHERE `id_session` = ".$supp_session);
			$result = spip_query("DELETE FROM `spip_ecommerce_sessions` WHERE `id_session` = ".$supp_session);
			}
		$result = spip_query("DELETE FROM `spip_ecommerce_paniers` WHERE `id_session` = ".$supp_session);
		$result = spip_query("DELETE FROM `spip_ecommerce_sessions` WHERE `id_session` = ".$supp_session);
		}

//
// Icones retour et suppression
//
	echo "<div style='text-align:$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "rien.gif",'right');
	$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('boutique')));
//	$link = generer_url_ecrire('boutique_delete','supp_session='."$id_session&retour=".urlencode($retour));
	echo "<div style='clear:both;'></div>";
	echo "</div>";
//
// FIN
//
	fin_page();
	}
?>
