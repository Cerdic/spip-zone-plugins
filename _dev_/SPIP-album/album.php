<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	*
	* SPIP-ALBUM : Programme d'affichage de photos
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Definition du chemin du plugin SPIP-Album -------------------------
if (!defined('_DIR_PLUGIN_ALBUM')) { // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ALBUM',(_DIR_PLUGINS.end($p).'/'));
}

//
// -- fonction specifique pour afficher images locales ------------------
// Balise #IMGLOCAL - Merci à Triton-pointcentral pour ce code.
//
function balise_IMGLOCAL($p) {
	if ($p->param && !$p->param[0][0]) {
		$p->code = calculer_liste ($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
		$alt =  calculer_liste ($p->param[0][2],
								$p->descr,
								$p->boucles,
								$p->id_boucle);
		// autres filtres
		array_shift($p->param);
	}
	
	// recherche du chemin de l'image (comme #CHEMIN)
	$p->code = 'find_in_path(' . $p->code . ')';
	// passage en image
	$p->code = '"<img src=\'".' . $p->code . '."\' alt=\'".' . $alt . '."\' />"';
	
	#$p->interdire_scripts = true;
	return $p;
}


function album_insert_head($flux) {
	$flux .= "<!-- Element necessaire au plugin SPIP-Album -->\n".
		'<link rel="stylesheet" href="'._DIR_PLUGIN_ALBUM.'css/lightbox.css" media="projection, screen, tv" />'."\n".
		'<link rel="stylesheet" href="'._DIR_PLUGIN_ALBUM.'css/album.css" media="projection, screen, tv" />'."\n".
		'<script type="text/javascript" src="'._DIR_PLUGIN_ALBUM.'javascript/prototype.js"></script>'."\n".
		'<script type="text/javascript" src="'._DIR_PLUGIN_ALBUM.'javascript/scriptaculous.js?load=effects"></script>'."\n".
		'<script type="text/javascript" src="'._DIR_PLUGIN_ALBUM.'javascript/lightbox.js"></script>'."\n".
		'<script type="text/javascript" src="'._DIR_PLUGIN_ALBUM.'javascript/speedalbum.js"></script>'."\n";
	return $flux;
}

function album_header_prive($flux){
	$exec = _request('exec');
	if ($exec == 'articles'){
//		$flux = album_insert_head($flux);	
	}
	return $flux;
}
?>