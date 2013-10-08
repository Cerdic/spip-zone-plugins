<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */

// demander a SPIP de definir 'type-page' dans le contexte du premier squelette
define('_DEFINIR_CONTEXTE_TYPE_PAGE',true);
define('_ZPIP',true);
// differencier le cache,
// la verification de credibilite de var_zajax sera faite dans public_styliser_dist
// mais ici on s'assure que la variable ne permet pas de faire une inclusion arbitraire
// avec un . ou un /
if ($z = _request('var_zajax')
  AND !preg_match(",[^\w-],",$z)) {
	$GLOBALS['marqueur'] .= "$z:";
	$GLOBALS['flag_preserver'] = true;
}
else {
	// supprimer cette variable dangeureuse
	set_request('var_zajax','');
}

/**
 * html Pour pouvoir masquer les logos sans les downloader en petit ecran
 * il faut le mettre dans un conteneur parent que l'on masque
 * http://timkadlec.com/2012/04/media-query-asset-downloading-results/
 *
 * on fixe le height en CSS pour que le height:auto par defaut sur img ne s'applique pas ici
 * (un logo est toujours plus petit que l'ecran, donc max-width:100% ne fait rien)
 * Pour le reduire dans une liste en colonne par exemple il faut faire en css
 * max-width:50px;height:auto!important;
 *
 * @param $logo
 * @return string
 */
function responsive_logo($logo){
	if (!function_exists('extraire_balise'))
		include_spip('inc/filtres');
	if (!$logo
	  OR !$img = extraire_balise($logo,"img"))
		return $logo;
	list($h,$w) = taille_image($img);
	$src = extraire_attribut($img,"src");
	$class = extraire_attribut($img,"class");

	$ratio = round($h*100/$w,2);
	return "<span class='$class' style=\"width:{$w}px;\"><span class=\"img\" style=\"display:block;position:relative;height:0;width:100%;padding-bottom:{$ratio}%;overflow:hidden;background:url($src) no-repeat center;background-size:100%;\"> </span></span>";
}

?>