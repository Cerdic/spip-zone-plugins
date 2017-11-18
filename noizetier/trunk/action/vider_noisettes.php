<?php
/**
 * Ce fichier contient l'action `vider_noisettes` lancée par un utilisateur pour
 * supprimer de façon sécurisée les noisettes d'un conteneur, d'une page ou d'un objet.
 *
 * @package SPIP\NOIZETIER\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * toutes les noisettes d'un conteneur, d'une page ou d'un objet.
 * Le compositions de page explicites ou virtuelles (créée par le noizetier) sont aussi prises en compte.
 *
 * Cette action est réservée aux utilisateurs autorisés.
 * Elle nécessite des arguments dont la liste dépend du contexte.
 *
 * @return void
 */
function action_vider_noisettes_dist() {

	// Securisation et autorisation.
	// Les arguments attendus dépendent du contexte et la chaine peut prendre les formes suivantes:
	// - conteneur:id_conteneur/page:id_page, pour supprimer toutes les noisettes d'un conteneur d'une page
	// - conteneur:id_conteneur/objet:id_objet:type_objet, pour supprimer toutes les noisettes d'un conteneur pour un objet précis
	// - page:id_page, pour supprimer toutes les noisettes d'une page (y compris les compositions)
	// - objet:id_objet:type_objet, pour supprimer toutes les noisettes d'un objet précis
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	if ($arguments) {
		$id_conteneur = '';
		if (strpos($arguments, ',') !== false) {
			// L'action consiste à supprimer les noisettes d'un conteneur donné.
			// On extrait l'id du conteneur et les identifiants de la page ou de l'objet pour
			// vérifier les autorisations.
			list($conteneur, $page) = explode(',', $arguments);
			list(, $id_conteneur) = explode(':', $conteneur);
		} else {
			$page = $arguments;
		}

		// On construit le tableau $options pour l'autorisation et on détermine l'invalideur.
		// L'identifiant de la page ou de l'objet se reconnait par le nombre d'éléments.
		$identifiants = explode(':', $page);
		if (count($identifiants) > 2) {
			$options['objet'] = $identifiants[2];
			$options['id_objet'] = $identifiants[1];
			$invalideur = "id='{$options['objet']}/{$options['id_objet']}'";
		} else {
			$options['page'] = $identifiants[1];
			$invalideur = "id='page/{$options['page']}'";
		}

		// Verification des autorisations.
		if (!autoriser('configurerpage', 'noizetier', '', 0, $options)) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Suppression des noisettes concernées.
		if ($id_conteneur) {
			// Cas du conteneur
			include_spip('inc/ncore_conteneur');
			conteneur_vider('noizetier', $id_conteneur);
		} else {
			// Cas de la page ou de l'objet
			// -- On ne s'occupe que des noisettes du noizetier
			$where[] = 'plugin=' . sql_quote('noizetier');
			if (!empty($options['objet'])) {
				// Suppression des noisettes d'un objet d'un type donnée
				$where[] = 'objet=' . sql_quote($options['objet']);
				$where[] = 'id_objet=' . intval($options['id_objet']);
			} else {
				// Suppression des noisettes d'une page.
				// Il faut tenir compte du cas où la page est une composition auquel cas le type et la
				// composition sont insérées séparément dans la table spip_noizetier.
				$identifiants = explode('-', $options['page'], 2);
				$where[] = 'type=' . sql_quote($identifiants[0]);
				if (isset($identifiants[1])) {
					$where[] = 'composition=' . sql_quote($identifiants[1]);
				} else {
					$where[] = 'composition=' . sql_quote('');
				}
			}

			// Suppression en base de données
			sql_delete('spip_noizetier', $where);
		}

		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur($invalideur);
	}
}
