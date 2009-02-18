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

function bando_images_background(){
	include_spip('inc/bandeau');
	// recuperer tous les boutons et leurs images
	$boutons = definir_barre_boutons(definir_barre_contexte(),true,false);

	$res = "";
	foreach($boutons as $page => $detail){
		if ($detail->icone AND strlen(trim($detail->icone)))
		$res .="\n.avec_icones #bando1_$page {background-image:url(".$detail->icone.");}";
		if (is_array($detail->sousmenu))
		foreach($detail->sousmenu as $souspage=>$sousdetail)
		if ($sousdetail->icone AND strlen(trim($sousdetail->icone)))
		$res .="\n.avec_icones #bando2_$souspage {background-image:url(".$sousdetail->icone.");}";
	}
	return $res;
}