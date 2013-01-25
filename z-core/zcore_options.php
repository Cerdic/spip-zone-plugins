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
	// gif transparent 1px
	// http://proger.i-forge.net/The_smallest_transparent_pixel/eBQ
	$gif = "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
	include_spip('inc/filtres');
	$img = extraire_balise($logo,"img");
	list($h,$w) = taille_image($img);
	$src = extraire_attribut($img,"src");
	$style = extraire_attribut($img,"style");
	$style = "background:url($src) no-repeat center;background-size:100%;height:{$h}px;$style";
	$class = extraire_attribut($img,"class");
	$img = inserer_attribut($img,"src",$gif);
	$img = inserer_attribut($img,"style",$style);
	$img = inserer_attribut($img,"class","");

	return "<span class='$class'>$img</span>";
}
?>