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

function public_connect_db()
	{
	if (!$link = mysql_connect('ilsalone.free.fr', 'ilsalone', '0range'))
		{
		echo 'Connexion impossible à mysql';
		exit;
		}
	if (!mysql_select_db('ilsalone', $link))
		{
		echo 'Sélection de base de données impossible';
		exit;
		}
	}

?>
