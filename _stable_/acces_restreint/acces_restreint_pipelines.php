<?php
if (!defined('_DIR_PLUGIN_ACCESRESTREINT')){ // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ACCESRESTREINT',(_DIR_PLUGINS.end($p)));
}

	/* public static */
	function AccesRestreint_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
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
		$exec = $flux['args']['exec'];
		if ($exec == 'auteurs_edit'){
			include_spip('inc/acces_restreint_gestion');
			$id_auteur = $flux['args']['id_auteur'];
			$nouv_zone = _request('nouv_zone');
			$supp_zone = _request('supp_zone');
			// le formulaire qu'on ajoute
			global $connect_statut;
			$flux['data'] .= AccesRestreint_formulaire_zones('auteurs', $id_auteur, $nouv_zone, $supp_zone, $connect_statut == '0minirezo', generer_url_ecrire('auteurs_edit',"id_auteur=$id_auteur"));
		}
		return $flux;
	}

?>