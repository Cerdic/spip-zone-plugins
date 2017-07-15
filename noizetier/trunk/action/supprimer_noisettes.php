<?php
/**
 * Ce fichier contient l'action `supprimer_noisettes` lancée par un utilisateur pour
 * supprimer de façon sécurisée une ou plusieurs noisettes.
 *
 * @package SPIP\NOIZETIER\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * une noisette donnée ou toutes les noisettes d'une page ou d'un bloc d'une page.
 * Le compositions de page explicites ou virtuelles (créée par le noizetier) sont aussi prises en compte.
 *
 * Cette action est réservée aux webmestres. Elle nécessite des arguments dont la liste dépend
 * du contexte.
 *
 * @uses supprimer_noisettes()
 *
 * @return void
 */
function action_supprimer_noisettes_dist() {

	// Securisation et autorisation.
	// Les arguments attendus dépendent du contexte et la chaine peut prendre les formes suivantes:
	// - noisette:id_noisette, pour supprimer un noisette connue par son id
	// - page:id_page, pour supprimer toutes les noisettes d'une page (y compris les compositions)
	// - objet:type_objet:id_objet, pour supprimer toutes les noisettes d'une page pour un objet précis
	// - page:id_page/bloc:id_bloc, pour supprimer toutes les noisettes d'un bloc d'une page (y compris les compositions)
	// - objet:id_objet:type_objet/bloc:id_bloc, pour supprimer toutes les noisettes d'un bloc d'une page pour un objet précis
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	// -- récupération de la page ou de l'objet
	if ($arguments) {
		$bloc = '';
		$contexte = 'page';
		if (strpos($arguments, '/') !== false) {
			// L'action consiste à supprimer les noisettes d'un bloc donné.
			// On extrait le bloc et on renvoie dans $arguments uniquement l'élément
			// concerné par la suppression.
			list($arguments, $bloc) = explode('/', $arguments);
			list(, $bloc) = explode(':', $bloc);
			$contexte = 'bloc';
		}

		// Quelque soit le contexte de suppression, à ce stade, $arguments contient l'élément dont on va
		// supprimer les noisettes.
		// On l'extrait à son tour et on complète le contexte.
		$element = explode(':', $arguments);
		if (count($element) > 1) {
			// L'id de la noisette ou de l'objet ou l'identifiant de la page est toujours passé.
			// Le type ne concerne qu'un objet.
			$identifiant['id'] = $element[1];
			$identifiant['type'] = '';

			$options = array();
			if ($element[0] == 'noisette') {
				$contexte = 'noisette';
				// Dans le cas d'une suppression d'une noisette, les données de la page ne sont
				// pas fournies en argument, il faut les lire en base de données.
				$select = array('type', 'composition', 'objet', 'id_objet');
				$where = array('id_noisette=' . intval($identifiant['id']));
				$noisette = sql_fetsel($select, 'spip_noisettes', $where);
				if ($noisette['type']) {
					$options['page'] = $noisette['composition']
						? $noisette['type'] . '-' . $noisette['composition']
						: $noisette['type'];
				} else {
					$options['objet'] = $noisette['objet'];
					$options['id_objet'] = $noisette['id_objet'];
				}
			} else {
				// Pour un objet, l'id est complété par le type d'objet.
				if (count($element) > 2) {
					$identifiant['type'] = $element[2];
				}

				// On construit le tableau $options pour l'autorisation.
				if ($identifiant['type']) {
					$options['objet'] = $identifiant['type'];
					$options['id_objet'] = $identifiant['id'];
				} else {
					$options['page'] = $identifiant['id'];
				}
			}

			// Verification des autorisations
			if (!autoriser('configurerpage', 'noizetier', '', 0, $options)) {
				include_spip('inc/minipres');
				echo minipres();
				exit();
			}

			// Suppression des noisettes concernées. On vérifie la sécurité des id numériques.
			supprimer_noisettes($contexte, $identifiant, $bloc);
		}
	}
}



/**
 * Supprime de la base de données ue noisette donnée ou toutes les noisettes liées à une page
 * ou à un bloc d'une page.
 *
 * @param string	$contexte
 * 		Contexte de la suppression:
 * 		- 'noisette' : suppression d'une noisette identifiée par son id
 * 		- 'bloc'     : suppression de toutes les noisettes d'un bloc d'une page ou d'un objet associé à une page
 * 		- 'page'     : suppression de toutes les noisettes d'une page ou d'un objet associé à une page
 * @param array		$identifiant
 * 		Tableau contenant les identifiants de l'objet concerné par la suppression:
 * 		- 'id'   : identifiant de la noisette (id_noisette), de la page (type) ou de l'objet (id_objet)
 * 		- 'type' : type d'objet si l'index existe
 * @param string	$bloc
 * 		Identifiant du bloc ou chaine vide sinon.
 *
 * return void
 */
function supprimer_noisettes($contexte, $identifiant, $bloc) {

	$where = array();
	if ($contexte == 'noisette') {
		// Suppression d'une noisette
		$where[] = 'id_noisette=' . intval($identifiant['id']);
		$invalideur = "id='noisette/{$identifiant['id']}'";
	} else {
		if ($identifiant['type']) {
			// Suppression des noisettes d'un objet d'un type donnée
			$where[] = 'objet=' . sql_quote($identifiant['type']);
			$where[] = 'id_objet=' . intval($identifiant['id']);
			$invalideur = "id='{$identifiant['type']}/{$identifiant['id']}'";
		} else {
			// Suppression des noisettes d'une page.
			// Il faut tenir compte du cas où la page est une composition auquel cas le type et la
			// composition sont insérées séparément dans la table spip_noisettes.
			$page = explode('-', $identifiant['id'], 2);
			$where[] = 'type=' . sql_quote($page[0]);
			if (isset($page[1])) {
				$where[] = 'composition=' . sql_quote($page[1]);
			} else {
				$where[] = 'composition=' . sql_quote('');
			}
			$invalideur = "id='page/{$identifiant['id']}'";
		}
		if ($contexte == 'bloc') {
			// Limitation à un bloc donné
			$where[] = 'bloc=' . sql_quote($bloc);
		}
	}

	// Suppression en base de données
	sql_delete('spip_noisettes', $where);

	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur($invalideur);
}
