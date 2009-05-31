<?php
/*
 * boutique
 * version plug-in de spip_boutique
 *
 * Auteur :
 * 
 * 
 * © 2005,2006 - Distribue sous licence GNU/GPL
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