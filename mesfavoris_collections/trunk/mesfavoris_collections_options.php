<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Traiter le formulaire CVT
 * 
 * @param  string $objet     
 * @param  int $id_objet  
 * @param  string $categorie 
 * @return array            
 */
function formulaires_favori_traiter($objet, $id_objet, $categorie='') {
	$res = array('message_ok'=>' ');
	
	if ($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])) {
		include_spip('inc/mesfavoris');
		
		if (!is_null(_request('ajouter'))) {
			$res['id_favori'] = mesfavoris_ajouter($id_objet, $objet, $id_auteur, $categorie);
		}
		
		if (!is_null(_request('retirer'))) {
			$conditions = array(
				'id_objet'  => $id_objet,
				'objet'     => $objet,
				'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
				'categorie' => $categorie,
			);
			
			if ($id_favoris_collection = intval(_request('id_favoris_collection'))) {
				$conditions['id_favoris_collection'] = $id_favoris_collection;
			}
			
			mesfavoris_supprimer($conditions);
		}
	}
	
	return $res;
}
