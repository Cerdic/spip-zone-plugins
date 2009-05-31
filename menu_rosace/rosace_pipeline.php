<?php
######################################################################
# Menu Rosace                                   
# Auteur: Dominique (Dom) Lepaisant - Frederic TARAUD - OCTOBRE 2008                 
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
# Pour plus de details voir le fichier COPYING.txt                   
######################################################################
if (!defined("_ECRIRE_INC_VERSION")) return;


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ROSACE',(_DIR_PLUGINS.end($p)));

# repertoire icones . tispipskelet_conf/img_pack/
if (!defined("_DIR_IMG_ROSACE")) {
	define('_DIR_IMG_ROSACE', _DIR_PLUGIN_ROSACE.'img_pack/');
}

function rosace_ajouterOnglets($flux) {
	include_spip('inc/urls');
	include_spip('inc/utils');

	global $connect_statut
		, $connect_toutes_rubriques
		;

	if(
		($flux['args'] == 'configuration')
		&& ($connect_statut == '0minirezo')
		&& $connect_toutes_rubriques
		) {
		$flux['data'][_ROSACE_PREFIX] = new Bouton( 
			_DIR_PLUGIN_ROSACE."/img_pack/logo_rosace_24.png"
			, 'Menu Rosace'
			, generer_url_ecrire("cfg&cfg=rosace")
			)
			;
	}

	return ($flux);
}
// css prive
	function rosace_header_prive($flux) {
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_ROSACE.'css/rosace_prive.css" />'."\n";
//		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_ROSACE.'css/rosace_public.css" />'."\n";
		return $flux;
	}
	
// css public
	function rosace_insert_head($flux) {
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_ROSACE.'css/rosace_public.css" />'."\n";
		return $flux;
	}


?>
