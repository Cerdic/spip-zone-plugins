<?php
/**
 * Ce fichier contient l'API de gestion des objets configurables par le noiZetier.
 *
 * @package SPIP\NOIZETIER\OBJET\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie la description complète ou uniquement une information précise pour un objet donné.
 * Cette fonction est utilisable dans le public via la balise #NOIZETIER_OBJET_INFOS.
 *
 * @api
 *
 * @param string $type_objet
 *        Type de l'objet comme `article`.
 * @param string $id_objet
 *        Id de l'objet.
 * @param string $information
 *        Champ précis à renvoyer ou chaîne vide pour renvoyer toutes les champs de l'objet.
 *
 * @return mixed
 *         La description complète sous forme de tableau ou l'information précise demandée.
 */
function noizetier_objet_lire($type_objet, $id_objet, $information = '') {

	static $description_objet = array();

	if ($type_objet and intval($id_objet) and !isset($description_objet[$type_objet][$id_objet])) {
		include_spip('inc/quete');
		include_spip('base/objets');
		$description = array();

		// On calcule le titre de l'objet à partir de la fonction idoine
		$description['titre'] = generer_info_entite($id_objet, $type_objet, 'titre');

		// On recherche le logo de l'objet si il existe sinon on stocke le logo du type d'objet
		// (le chemin complet)
		$description['logo'] = '';
		if ($type_objet != 'document') {
			$logo_infos = quete_logo(id_table_objet($type_objet), 'on', $id_objet, 0, false);
			$description['logo'] = isset($logo_infos['src']) ? $logo_infos['src'] : '';
		}
		if (!$description['logo']) {
			$description['logo'] = chemin_image("${type_objet}.png");
		}

		// On récupère le nombre de noisette déjà configurées dans l'objet.
		$description['noisettes'] = 0;
		$from = array('spip_noisettes');
		$where = array(
			'plugin=' . sql_quote('noizetier'),
			'objet=' . sql_quote($type_objet),
			'id_objet=' . intval($id_objet)
		);
		if ($noisettes = sql_countsel($from, $where)) {
			$description['noisettes'] = $noisettes;
		}

		// On rajoute les blocs du type de page dont l'objet est une instance
		include_spip('inc/noizetier_page');
		$description['blocs'] = noizetier_page_lister_blocs($type_objet);

		// On sauvegarde finalement la description complète.
		$description_objet[$type_objet][$id_objet] = $description;
	}

	// On retourne les informations sur l'objet demandé.
	if (!$information) {
		$retour = isset($description_objet[$type_objet][$id_objet])
			? $description_objet[$type_objet][$id_objet]
			: array();
	} else {
		$retour = isset($description_objet[$type_objet][$id_objet][$information])
			? $description_objet[$type_objet][$id_objet][$information]
			: '';
	}

	return $retour;
}

/**
 * Lister les contenus ayant des noisettes spécifiquement configurées pour leur page.
 * Cette fonction est utilisable dans le public via la balise #NOIZETIER_OBJET_LISTE.
 *
 * @api
 *
 * @param array $filtres
 * 	      Liste des champs sur lesquels appliquer les filtres des objets.
 *
 * @return array|string
 * 		   Tableau des descriptions de chaque objet trouvés. Ce tableau est éventuellement filtré sur
 *         un ou plusieurs champs de la description.
 */
function noizetier_objet_repertorier($filtres = array()) {

	static $objets = null;

	if (is_null($objets)) {
		// On récupère le ou les objets ayant des noisettes dans la table spip_noisettes.
		$from = array('spip_noisettes');
		$select = array('objet', 'id_objet', "count(type_noisette) as 'noisettes'");
		$where = array(
			'plugin=' . sql_quote('noizetier'),
			'id_objet>0'
		);
		$group = array('objet', 'id_objet');
		$objets_configures = sql_allfetsel($select, $from, $where, $group);
		if ($objets_configures) {
			foreach ($objets_configures as $_objet) {
				// On ne retient que les objets dont le type est activé dans la configuration du plugin.
				if (noizetier_objet_type_active($_objet['objet'])) {
					$description = noizetier_objet_lire($_objet['objet'], $_objet['id_objet']);
					if ($description) {
						// Si un filtre existe on teste le contenu de l'objet récupéré avant de le garder
						// sinon on le sauvegarde immédiatement.
						$objet_a_retenir = true;
						if ($filtres) {
							foreach ($filtres as $_critere => $_valeur) {
								if (isset($description[$_critere]) and ($description[$_critere] == $_valeur)) {
									$objet_a_retenir = false;
									break;
								}
							}
						}
						if ($objet_a_retenir) {
							$objets[$_objet['objet']][$_objet['id_objet']] = $description;
						}
					}
				}
			}
		}
	}

	return $objets;
}

/**
 * Détermine si un type d'objet est activé dans la configuration du noiZetier.
 * Si oui, ses objets peuvent recevoir une configuration de noisettes.
 *
 * @api
 *
 * @param string $type_objet
 * 		Type d'objet SPIP comme article, rubrique...
 *
 * @return boolean
 * 		True si le type d'objet est activé, false sinon.
 */
function noizetier_objet_type_active($type_objet) {

	static $tables_actives = null;
	$est_active = false;

	// Si la liste des tables d'objet actives est null on la calcule une seule fois
	if ($tables_actives === null) {
		include_spip('inc/config');
		$tables_actives = array_map('objet_type', lire_config('noizetier/objets_noisettes', array()));
	}

	// Si la liste est non vide, on détermine si le type d'objet est bien activé.
	if ($tables_actives and in_array($type_objet, $tables_actives)) {
		$est_active = true;
	}

	return $est_active;
}


/**
 * Détermine, pour un objet donné, la liste des blocs ayant des noisettes incluses et renvoie leur nombre.
 *
 * @api
 *
 * @param string $objet
 * 	      Le type d'objet comme `article`.
 * @param int    $id_objet
 * 	      L'id de l'objet.
 *
 * @return array
 * 	       Tableau des nombre de noisettes incluses par bloc de la forme [bloc] = nombre de noisettes.
 */
function noizetier_objet_compter_noisettes($objet, $id_objet) {

	static $blocs_compteur = array();

	if (!isset($blocs_compteur["${objet}-${id_objet}"])) {
		// Initialisation des compteurs par bloc
		$nb_noisettes = array();

		// Le nombre de noisettes par bloc doit être calculé par une lecture de la table spip_noisettes.
		$from = array('spip_noisettes');
		$select = array('bloc', "count(type_noisette) as 'noisettes'");
		// -- Construction du where identifiant précisément le type et la composition de la page
		$where = array(
			'plugin=' . sql_quote('noizetier'),
			'objet=' . sql_quote($objet),
			'id_objet=' . intval($id_objet)
		);
		$group = array('bloc');
		$blocs_non_vides = sql_allfetsel($select, $from, $where, $group);
		if ($blocs_non_vides) {
			// On formate le tableau [bloc] = nb noisettes
			$nb_noisettes = array_column($blocs_non_vides, 'noisettes', 'bloc');
		}

		// Sauvegarde des compteurs pour les blocs concernés.
		$blocs_compteur["${objet}-${id_objet}"] = $nb_noisettes;
	}

	return $blocs_compteur["${objet}-${id_objet}"];
}
