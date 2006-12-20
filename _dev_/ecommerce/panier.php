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
//	echo "<p><strong>".("panier.php [$retour]")."</strong> ";
//	exit;
//
// FIN
//

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

function choice ($id_session)
	{
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$sql = "SELECT `id_session` FROM `spip_ecommerce_sessions` where `id_session`='$id_session'";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	$num_rows = mysql_num_rows($result);
	if ($num_rows == 0)
		{
		$sql = "INSERT INTO `spip_ecommerce_sessions` (`id_session`, `statut`) VALUES ('$id_session', 'open')";
		$result = mysql_query($sql, $link);
		if (!$result) 
			{
			echo "Erreur DB, impossible d'effectuer une requête\n";
			echo 'Erreur MySQL : ' . mysql_error();
			exit;
			}
		}
	mysql_close($link);
	}

function add($id_session,$id_article,$pointure,$quantite)
	{
	global $nogo ;

	$nogo="" ;
echo "$nogo" ;
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$sql = "SELECT `ps` FROM `spip_articles` where `id_article`='$id_article'";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	while ($row = mysql_fetch_array($result)) 
		{
		$ps = $row['ps'];
		}
	if (!strcmp ($ps, "Unique"))
		{
		$nogo=0;
		$pointure=99 ;
		}
	else
		{
		$position=strpos ($ps, $pointure, $offset);
		if ($position === false)
			{
			$nogo=1;
			}
		else
			{
			$nogo=0;
			}
		}
	$sql = "SELECT `statut` FROM `spip_ecommerce_sessions` where `id_session`='$id_session'";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	while ($row = mysql_fetch_array($result)) 
		{
		$statut = $row['statut'];
		}
	if (!strcmp ($statut, "create") || !strcmp ($statut, "open"))
		{
		if (!$nogo)
			{
			$sql = "UPDATE `spip_ecommerce_sessions` SET 
				`statut` = 'open',
				`maj` = NOW() 
				WHERE `id_session` = $id_session LIMIT 1"; 
			$result = mysql_query($sql, $link);
			if (!$result) 
				{
				echo "Erreur DB, impossible d'effectuer une requête\n";
				echo 'Erreur MySQL : ' . mysql_error();
				exit;
				}
			$nogo=0;
			}
		}
	else
		$nogo=2;
	if (!$nogo)
		{
		$sql = "INSERT INTO `spip_ecommerce_paniers` (`id_session`, `id_article`, `pointure`, `quantite`) VALUES ('$id_session', '$id_article', '$pointure', '$quantite')";
		$result = mysql_query($sql, $link);
		if (!$result) 
			{
			echo "Erreur DB, impossible d'effectuer une requête\n";
			echo 'Erreur MySQL : ' . mysql_error();
			exit;
			}
		}
	mysql_close($link);
	}


function delete($id_session,$id_article,$pointure,$quantite)
	{
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$sql = "DELETE FROM `spip_ecommerce_paniers` WHERE `id_session` = $id_session AND `id_article` = $id_article AND `pointure` = $pointure AND `quantite` = quantite LIMIT 1";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	mysql_close($link);
	}

function cancel ($id_session)
	{
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$sql = "SELECT `statut` FROM `spip_ecommerce_sessions` where `id_session`='$id_session'";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	while ($row = mysql_fetch_array($result)) 
		{
		$statut = $row['statut'];
		}
	if (!strcmp ($statut, "create") || !strcmp ($statut, "open"))
		{
		$sql = "DELETE FROM `spip_ecommerce_paniers` WHERE `id_session` = $id_session";
		$result = mysql_query($sql, $link);
		if (!$result) 
			{
			echo "Erreur DB, impossible d'effectuer une requête\n";
			echo 'Erreur MySQL : ' . mysql_error();
			exit;
			}
		$sql = "UPDATE `spip_ecommerce_sessions` SET 
			`statut` = 'cancel',
			`maj` = NOW() 
			WHERE `id_session` = $id_session LIMIT 1"; 
		$result = mysql_query($sql, $link);
		if (!$result) 
			{
			echo "Erreur DB, impossible d'effectuer une requête\n";
			echo 'Erreur MySQL : ' . mysql_error();
			exit;
			}
		}
	if (!strcmp ($statut, "submit"))
		{
		$sql = "UPDATE `spip_ecommerce_sessions` SET 
			`statut` = 'abort',
			`maj` = NOW() 
			WHERE `id_session` = $id_session LIMIT 1"; 
		$result = mysql_query($sql, $link);
		if (!$result) 
			{
			echo "Erreur DB, impossible d'effectuer une requête\n";
			echo 'Erreur MySQL : ' . mysql_error();
			exit;
			}
		}
	mysql_close($link);
	}


if ($choisir)
	{
	choice ($id_session);
	}

if ($valider)
	{
	if ($pointure)
		{
		choice ($id_session);
		add ($id_session,$id_article, $pointure, $quantite);
		}
	}

if ($delete)
	{
	delete ($id_session,$id_article, $pointure, $quantite);
	}

if ($annuler)
	{
	cancel ($id_session);
	}

$fond="panier";
$delais = 24 * 3600;
include ("inc-public.php3");
?>
