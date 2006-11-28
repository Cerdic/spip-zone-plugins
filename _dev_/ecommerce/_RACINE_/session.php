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
//	echo "<p><strong>".("boutique.php [Appel-> id_session=$id_session]")."</strong> ";
//	echo "<p><strong>".("boutique.php [Appel-> valider=$valider]")."</strong> ";
//	echo "<p><strong>".("boutique.php [Appel-> annuler=$annuler]")."</strong> ";
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

$fond="session";

if ($annuler)
	{
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
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
	mysql_close($link);
	$fond="panier" ;
	}

if ($payer)
	{
	$go=1 ;
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$sql = "SELECT count(id_article) FROM `spip_ecommerce_paniers` where `id_session`='$id_session'";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}

	$num_rows = mysql_num_rows($result);
	if ($num_rows > 0)
		{
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
		if ((!strcmp ($statut, "create") || !strcmp ($statut, "open")) && $go==1)
			{
			$go=1 ;
			if (!strcmp ($nom, "") || !$go)
				{
				$nom="vide" ;
				$go=0 ;
				}
			if (!strcmp ($prenom, "") || !$go)
				{
				$prenom="vide" ;
				$go=0 ;
					}
			if (!strcmp ($adresse_facturation, "") || !$go)
				{
				$adresse_facturation="vide" ;
				$go=0 ;
				}
			if (!strcmp ($ville_facturation, "") || !$go)
				{
				$ville_facturation="vide" ;
				$go=0 ;
				}
			if (!strcmp ($code_postal_facturation, "") || !$go)
				{
				$code_postal_facturation="vide" ;
				$go=0 ;
				}
			if (!strcmp ($pays_facturation, "") || !$go)
				{
				$pays_facturation="vide" ;
				$go=0 ;
				}
			if (!strcmp ($zone, "") || !$go)
				{
				$region="vide" ;
				$go=0 ;
				}
			if (!strcmp ($telephone, "") || !$go)
				{
				$telephone="vide" ;
				}
			if (!strcmp ($email, "") || !$go)
				{
				$email="vide" ;
				$news=0;
				$go=0 ;
				}
			if (!strcmp ($adresse_livraison, "") || !$go)
				{
				$adresse_livraison="vide" ;
				}
			if (!strcmp ($code_postal_livraison, "") || !$go)
				{
				$code_postal_livraison="vide" ;
				}
			if (!strcmp ($ville_livraison, "") || !$go)
				{
				$ville_livraison="vide" ;
				}
			if (!strcmp ($pays_livraison, "") || !$go)
				{
				$pays_livraison="vide" ;
				}
			$submit="submit" ;
			$nom=urlencode($nom) ;
			$prenom=urlencode($prenom) ;
			$adresse_livraison=urlencode($adresse_livraison);
			$code_postal_livraison=urlencode($code_postal_livraison);
			$ville_livraison=urlencode($ville_livraison);
			$email=urlencode($email) ;
			$telephone=urlencode($telephone);
			$adresse_facturation=urlencode($adresse_facturation);
			$code_postal_facturation=urlencode($code_postal_facturation);
			$ville_facturation=urlencode($ville_facturation);
			if ($go)
				{
				if (!$link = boutique_mysql_connect ())
					{
					echo 'Traitement mysql interrompu';
					exit;
					}
				$sql = "UPDATE `spip_ecommerce_sessions` SET 
					`categorie` = '$categorie', 
					`nom` = '$nom', 
					`prenom` = '$prenom', 
					`email` = '$email',
					`news` = '$news',
					`adresse_livraison` = '$adresse_livraison',
					`code_postal_livraison` = '$code_postal_livraison',
					`ville_livraison` = '$ville_livraison', 
					`pays_livraison` = '$pays_livraison', 
					`zone` = '$zone', 
					`telephone` = '$telephone', 
					`adresse_facturation` = '$adresse_facturation',
					`code_postal_facturation` = '$code_postal_facturation',
					`ville_facturation` = '$ville_facturation', 
					`pays_facturation` = '$pays_facturation', 
					`statut` = '$submit',
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
			}
		go=2 ;
		}
	mysql_close($link);
	}

$delais = 24 * 3600;
$delais = 1;
include ("inc-public.php3");

?>
