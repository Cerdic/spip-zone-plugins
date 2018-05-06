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
			$description['logo'] = noizetier_icone_chemin("${type_objet}.png");
		}

		// On récupère le nombre de noisette déjà configurées dans l'objet.
		$description['noisettes'] = 0;
		$from = array('spip_noisettes');
		$where = array('objet=' . sql_quote($type_objet), 'id_objet=' . intval($id_objet));
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
		$where = array('id_objet>0');
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
 * Renvoie la liste des types d'objet ne pouvant pas être personnalisés car ne possédant pas
 * de page détectable par le noiZetier.
 *
 * @api
 * @filtre
 *
 * @return array|null
 */
function noizetier_objet_lister_exclusions() {

	static $exclusions = null;

	if (is_null($exclusions)) {
		$exclusions = array();
		include_spip('base/objets');

		// On récupère les tables d'objets sous la forme spip_xxxx.
		$tables = lister_tables_objets_sql();
		$tables = array_keys($tables);

		// On récupère la liste des pages disponibles et on transforme le type d'objet en table SQL.
		$where = array('composition=' . sql_quote(''), 'est_page_objet=' . sql_quote('oui'));
		$pages = sql_allfetsel('type', 'spip_noizetier_pages', $where);
		$pages = array_map('reset', $pages);
		$pages = array_map('table_objet_sql', $pages);

		// On exclut donc les tables qui ne sont pas dans la liste issues des pages.
		$exclusions = array_diff($tables, $pages);
	}

	return $exclusions;
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
