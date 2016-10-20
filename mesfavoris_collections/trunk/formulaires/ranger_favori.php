<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/session');
include_spip('inc/autoriser');
include_spip('action/editer_objet');

function formulaires_ranger_favori_saisies_dist($objet, $id_objet, $redirect='') {
	$saisies = array();
	$id_auteur = intval(session_get('id_auteur'));
	
	// On teste les collections de l'utilisateurice en cours
	if ($nb = sql_countsel('spip_favoris_collections', 'id_auteur = '.$id_auteur) and $nb > 0) {
		$saisies[] = array(
			'saisie' => 'favoris_collection',
			'options' => array(
				'nom' => 'id_favoris_collection',
				'label' => _T('favoris_collection:ranger_id_favoris_collection_label'),
				'id_auteur' => $id_auteur, // uniquement les collections de cet utilisateur
			),
		);
	}
	
	// Si on peut créer une nouvelle collection
	if (autoriser('creer', 'favoris_collection')) {
		$saisies[] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('favoris_collection:ranger_titre_label'),
			),
		);
	}
	
	return $saisies;
}

function formulaires_ranger_favori_traiter_dist($objet, $id_objet, $redirect='') {
	$retours = array();
	$id_auteur = intval(session_get('id_auteur'));
	
	// Si on redirige…
	if ($redirect) {
		$retours['redirect'] = $redirect;
	}
	
	// Si on a bien toujours un utilisateur au moment du traitement
	if ($id_auteur) {
		// On commence par chercher s'il existe déjà un favori NON classé pour ce contenu
		if (!$id_favori = intval(sql_getfetsel(
			'id_favori',
			'spip_favoris',
			array(
				'id_auteur = '.$id_auteur,
				'objet = '.sql_quote($objet),
				'id_objet = '.intval($id_objet),
				'id_favoris_collection = 0',
			)
		))) {
			$id_favori = 0;
		}
		
		// Si un titre est rempli, ça prend toujours la main pour une création
		if ($titre = _request('titre')) {
			// On commence par créer la collection
			if ($id_favoris_collection = objet_inserer('favoris_collection', 0, array('id_auteur' => $id_auteur))) {
				// On met le bon titre
				objet_modifier(
					'favoris_collection',
					$id_favoris_collection,
					array('titre' => $titre)
				);
			}
		}
		// Sinon s'il y a une collection de choisie dans le formulaire, on l'utilise
		else {
			$id_favoris_collection = intval(_request('id_favoris_collection'));
		}
		
		// Ensuite si on a bien une collection correcte, on classe le favori dedans
		if ($id_favoris_collection > 0) {
			// Si on a un favori non-classé sous la main, on le modifie
			if ($id_favori) {
				sql_updateq(
					'spip_favoris',
					array('id_favoris_collection' => $id_favoris_collection),
					'id_favori = '.$id_favori
				);
			}
			// Sinon on crée un nouveau favori dans la collection
			else {
				sql_insertq(
					'spip_favoris',
					array(
						'id_auteur' => $id_auteur,
						'objet' => $objet,
						'id_objet' => $id_objet,
						'id_favoris_collection' => $id_favoris_collection,
					)
				);
			}
		}
	}
	
	return $retours;
}
