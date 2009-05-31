<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Ajouter le bouton de menu config si on a le droit
 *
 * @param unknown_type $boutons_admin
 * @return unknown
 */
function accesrestreint_ajouter_boutons($boutons_admin) {
	// si on est admin
	if (autoriser('administrer','zone')) {
	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['configuration']->sousmenu['acces_restreint']= new Bouton(
		_DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif",  // icone
		_T('accesrestreint:icone_menu_config')	// titre
		);
	}
	return $boutons_admin;
}

/**
 * Ajouter la boite des zones sur la fiche auteur
 *
 * @param string $flux
 * @return string
 */
function accesrestreint_affiche_milieu($flux){
	switch($flux['args']['exec']) {
		case 'auteur_infos':
			$id_auteur = $flux['args']['id_auteur'];
			
			$flux['data'] .= 
			recuperer_fond('prive/editer/affecter_zones',array('id_auteur'=>$id_auteur));
			break;
	}
	return $flux;
}

?>