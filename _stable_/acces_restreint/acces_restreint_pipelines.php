<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;


if (!defined('_DIR_PLUGIN_ACCESRESTREINT')){ // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ACCESRESTREINT',(_DIR_PLUGINS.end($p)));
}

	/* public static */
	function AccesRestreint_ajouterBoutons($boutons_admin) {
		// si on est admin
		if (autoriser('modifier','zone')) {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['acces_restreint']= new Bouton(
			"../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif",  // icone
			_T('accesrestreint:icone_menu_config')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function AccesRestreint_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

	function AccesRestreint_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'auteurs_edit':
			case 'auteur_infos':
				$id_auteur = $flux['args']['id_auteur'];
				
				$flux['data'] .= 
				recuperer_fond('prive/editer/affectation_zones',array('id_auteur'=>$id_auteur));
				break;
		}
		return $flux;
	}

?>