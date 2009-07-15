<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!function_exists('bando_images_background')){
function bando_images_background(){
	include_spip('inc/bandeau');
	// recuperer tous les boutons et leurs images
	$boutons = definir_barre_boutons(definir_barre_contexte(),true,false);

	$res = "";
	foreach($boutons as $page => $detail){
		if ($detail->icone AND strlen(trim($detail->icone)))
			$res .="\n.navigation_avec_icones #bando1_$page {background-image:url(".$detail->icone.");}";
		$selecteur = ($page=='outils_rapides'?"":".navigation_avec_icones ");
		if (is_array($detail->sousmenu))
			foreach($detail->sousmenu as $souspage=>$sousdetail)
				if ($sousdetail->icone AND strlen(trim($sousdetail->icone)))
					$res .="\n$selecteur#bando2_$souspage {background-image:url(".$sousdetail->icone.");}";
	}
	return $res;
}
}
if (!function_exists('bando_style_prive_skin')){
function bando_style_prive_skin() {
	if ($f = find_in_skin('style_prive_skin.html'))
		return preg_replace(',[.]html$,Ui','',$f);
	return '';
}
}
