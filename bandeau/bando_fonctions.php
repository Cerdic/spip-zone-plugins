<?php
/*
 * Plugin Bando
 * (c) 2009 cedric
 * Distribue sous licence GPL
 *
 */
include_spip("public/slogan_balise");


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
					$res .="\n$selecteur.bando2_$souspage {background-image:url(".$sousdetail->icone.");}";
	}
	return $res;
}
}
if (!function_exists('bando_style_prive_theme')){
function bando_style_prive_theme() {
	if ($f = find_in_theme('style_prive_theme.html','',false,false))
		return preg_replace(',[.]html$,Ui','',$f);
	return '';
}
}
?>