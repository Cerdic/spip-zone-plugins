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

function boutique_verifier_base()
	{
	$version_base = 0.15;
	$current_version = 0.0;
	if (   (isset($GLOBALS['meta']['boutique_base_version']) )
			&& (($current_version = $GLOBALS['meta']['boutique_base_version'])==$version_base))
		return;
	include_spip('base/e-commerce');
	if ($current_version==0.0)
		{
		include_spip('base/create');
		include_spip('base/abstract_sql');
// attention on vient peut etre d'une table spip-boutique 1.8
		$desc = spip_abstract_showtable('spip_ecommerce_sessions','',true);
		if (isset($desc['field'])) 
			$current_version=0.1;
		else {
			creer_base();
			ecrire_meta('boutique_base_version',$current_version=$version_base);
			}
		$desc = spip_abstract_showtable('spip_ecommerce_paniers','',true);
		if (isset($desc['field'])) 
			$current_version=0.1;
		else {
			creer_base();
			ecrire_meta('boutique_base_version',$current_version=$version_base);
			}
		}
		ecrire_metas();
	}

function estceque_boutique_editable($id_session = 0) 
	{
	global $connect_statut;
	return $connect_statut == '0minirezo';
	}
	
function estceque_boutique_administrable($id_session = 0) 
	{
	global $connect_statut;
	return $connect_statut == '0minirezo';
	}

?>