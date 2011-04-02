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
		$menu = "configuration";
		$icone = "img_pack/zones-acces-24.gif";
		if (isset($boutons_admin['bando_configuration'])){
			$menu = "bando_configuration";
			$icone = "img_pack/zones-acces-24.gif";
		}
	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin[$menu]->sousmenu['acces_restreint']= new Bouton(
		_DIR_PLUGIN_ACCESRESTREINT.$icone,  // icone
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

/**
 * Ajouter la boite des zones sur la fiche de rubrique
 *
 * @param string $flux
 * @return string
 */
function accesrestreint_affiche_gauche($flux) {
	if ($flux['args']['exec'] == 'naviguer'){
		if (autoriser('administrer', 'zone', 0)) {
			$flux['data'] .= recuperer_fond('prive/inclure/acces_rubrique', $_GET);
		}
	}
	return $flux;
}

/**
 * Detecter les demande d'acces aux pages restreintes
 * et re-orienter vers une 401 si necessaire
 *
 * @param <type> $contexte
 * @return <type>
 */
function accesrestreint_page_indisponible($contexte){
	if ($contexte['status']=='404' AND isset($contexte['type'])){
		$objet = $contexte['type'];
		$table_sql = table_objet_sql($objet);
		$id_table_objet = id_table_objet($objet);
		if ($id = intval($contexte[$id_table_objet])){

			$publie = true;
			$restreint = false;

			$trouver_table = charger_fonction('trouver_table','base');
			$desc = $trouver_table($table_sql);
			if (isset($desc['field']['statut'])){
				$statut = sql_getfetsel('statut', $table_sql, "$id_table_objet=".intval($id));
				if ($statut!='publie')
					$publie = false;
			}
			
			include_spip('inc/autoriser');
			if ($publie AND !autoriser('voir',$objet,$id)){
				// c'est un contenu restreint
				$contexte['status'] = '401';
				$contexte['code'] = '401 Unauthorized';
				$contexte['fond'] = '401';
				$contexte['erreur'] = _T('accesrestreint:info_acces_restreint');
				$contexte['cible'] = self();
			}
		}
	}
	return $contexte;
}

/**
 * Permettre l'ajout de champs extras via le plugin Champs Extras 2 
 *
 * @param 
 * @return 
**/
function accesrestreint_objets_extensibles($objets){
		return array_merge($objets, array('zone' => _T('accesrestreint:titre_zones_acces')));
}

?>
