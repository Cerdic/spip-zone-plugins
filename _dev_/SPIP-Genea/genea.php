<?php
/*	*********************************************************************
	*
	* Copyright (c) 2006
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// --
function Genea_ajouter_boutons($boutons_admin){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]){
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['genea_naviguer']= new Bouton(
			""._DIR_PLUGIN_GENEA."/img_pack/arbre-24.png",  // icone
			'genea:titre' //titre
			);
	}
	return $boutons_admin;
}

// --
function Genea_ajouter_onglets($flux){
	$rubrique = $flux['args'];
	return $flux;
}

function Genea_header_prive($flux){
	return $flux;
}

function Genea_insert_head($flux){
	$flux .= "\n";
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_GENEA.'/css/genea.css" type="text/css" media="projection, screen, tv" />'."\n";
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_GENEA.'/javascript/mes_onglets.js"></script>'."\n";
	return $flux;
}

function Genea_exec_init($flux){
	return $flux;
}
?>