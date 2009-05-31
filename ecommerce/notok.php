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


if (!defined('_INCLUDE_ECOMMERCE')) 
	{
	# include necessaire a la boutique?
	@define('_DIR_INCLUDE', 'include/');
	include_once _DIR_INCLUDE.'ecommerce_mysql_engine.php'; 
	}
if (!defined('_ECRIRE_INC_VERSION')) 
	{
	# ou est l'espace prive ?
	@define('_DIR_RESTREINT_ABS', 'ecrire/');
	include_once _DIR_RESTREINT_ABS.'inc_version.php';
	}


if ($order_ref)
	{
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$sql = "UPDATE `spip_ecommerce_sessions` SET 
		`statut` = 'unsucessful',
		`maj` = NOW() 
		WHERE `code_session` = $order_ref LIMIT 1"; 
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	mysql_close($link);
	}

$fond="notok" ;
$delais = 24 * 3600;
include ("inc-public.php3");

?>
