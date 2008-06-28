<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

/**
 * Ajout du bouton "recherche multi-criteres" dans l'onglet "Editer"
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_REC_MC',(_DIR_PLUGINS.end($p)));

# repertoire icones REC_MC .. rec_mc/img_pack/
if (!defined("_DIR_IMG_REC_MC")) {
	define('_DIR_IMG_REC_MC', _DIR_PLUGIN_REC_MC.'img_pack/');
}

function rec_mc_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo"  && $GLOBALS["connect_toutes_rubriques"]) {
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['rec_mc']= new Bouton(
			"../"._DIR_IMG_REC_MC."rec_mc-24.png",  // icone
			'Recherche multi-criteres'	// titre
			);
	}
	return $boutons_admin;
}

function rec_mc_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


// css prive
	function rec_mc_header_prive($flux) {
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_REC_MC.'rec_mc_styles.css" />'."\n";
		return $flux;
	}
	
// css public
	function rec_mc_insert_head($flux) {
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_REC_MC.'rec_mc_styles_public.css" />'."\n";
		return $flux;
	}
	

?>
