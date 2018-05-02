<?php
/**
 * Ce fichier contient l'API N-Core de gestion des conteneurs.
 *
 * @package SPIP\NOIZETIER\CONTENEUR\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Détermine l'id du conteneur à partir des données d'une page, d'un objet ou d'une noisette conteneur.
 * Cette fonction est en fait une encapsalution de la fonction noizetier_conteneur_identifier() qui permet
 * de reconstituer le conteneur à partir des données du noizetier page, composition, objet et noisette.
 *
 * @api
 * @filtre
 *
 * @uses noizetier_conteneur_identifier()
 *
 * @param array|string $page
 * @param string       $bloc
 * @param int          $id_noisette
 *
 * @return string
 */
function noizetier_conteneur_composer($page, $bloc='') {

	$conteneur = array();

	// Construction du tableau associatif du conteneur.
	if (is_array($page)) {
		if (!empty($page['type_noisette']) and !empty($page['id_noisette'])) {
			// Le conteneur est une noisette.
			$conteneur = $page;
		} else {
			// Le conteneur est un objet.
			$conteneur['objet'] = $page['objet'];
			$conteneur['id_objet'] = $page['id_objet'];
			$conteneur['squelette'] = "${bloc}";
		}
	}
	else {
		// Le conteneur est une page ou une composition.
		$conteneur['squelette'] = "${bloc}/${page}";
	}

	// Calcul de l'identifiant du conteneur
	include_spip('ncore/noizetier');
	$id_conteneur = noizetier_conteneur_identifier('noizetier', $conteneur);

	return $id_conteneur;
}

/**
 * Détermine à partir de l'id du conteneur les données propres au noiZetier, à savoir, la page, l'objet ou la noisette
 * conteneur concernée.
 * Le tableau ainsi produit peut-être fourni aux autorisations concernant la manipulation des pages du noiZetier.
 *
 * @api
 * @filtre
 *
 * @uses noizetier_page_type()
 * @uses noizetier_page_composition()
 * @uses ncore_type_noisette_initialiser_dossier()
 *
 * @param string $id_conteneur
 *
 * @return array
 */
function noizetier_conteneur_decomposer($id_conteneur) {

	$conteneur = array();

	// Construction du tableau associatif propre au noizetier contenant les éléments
	// d'une conteneur mais aussi les éléments propres au noiZetier comme la page,
	// la composition, le type, l'objet ou la noisette conteneur.
	$elements = explode('|', $id_conteneur);
	if (count($elements) == 1) {
		// C'est une page ou une composition
		// -- le squelette
		$conteneur['squelette'] = $id_conteneur;
		// -- Page et bloc
		list($bloc, $page) = explode('/', $id_conteneur);
		$conteneur['bloc'] = $bloc;
		$conteneur['page'] = $page;
		// -- Type et composition
		include_spip('noizetier_fonctions');
		$conteneur['type'] = noizetier_page_type($conteneur['page']);
		$conteneur['composition'] = noizetier_page_composition($conteneur['page']);
	} else {
		if ($elements[1] == 'noisette') {
			// C'est une noisette
			// -- Type de noisette et id_noisette
			$conteneur['type_noisette'] = $elements[0];
			$conteneur['id_noisette'] = intval($elements[2]);
			// -- le squelette
			include_spip('ncore/ncore');
			$conteneur['squelette'] = ncore_type_noisette_initialiser_dossier('noizetier') . $conteneur['type_noisette'];
			// -- les éléments du conteneur de la noisette parent utiles pour les autorisations
			$select = array('type', 'composition', 'objet', 'id_objet', 'bloc');
			$where = array('id_noisette=' . $conteneur['id_noisette']);
			$noisette = sql_fetsel($select, 'spip_noisettes', $where);
			if ($noisette['type']) {
				$conteneur['type'] = $noisette['type'];
				$conteneur['composition'] = $noisette['composition'];
				$conteneur['page'] = $noisette['composition']
					? $noisette['type'] . '-' . $noisette['composition']
					: $noisette['type'];
			} else {
				$conteneur['objet'] = $noisette['objet'];
				$conteneur['id_objet'] = $noisette['id_objet'];
			}
			$conteneur['bloc'] = $noisette['bloc'];
		}
		else {
			// C'est un objet
			// -- le type d'objet et son id
			$conteneur['objet'] = $elements[1];
			$conteneur['id_objet'] = $elements[2];
			// -- le bloc
			$conteneur['bloc'] = $elements[0];
			// -- le squelette
			$conteneur['squelette'] = $conteneur['bloc'] . '/' . $conteneur['objet'];
		}
	}

	return $conteneur;
}
