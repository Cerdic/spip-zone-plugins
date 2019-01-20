<?php
/**
 * Ce fichier contient l'action `vider_page` lancée par un utilisateur pour
 * supprimer de façon sécurisée toutes les noisettes d'une page, d'une composition ou d'un objet.
 *
 * @package SPIP\NOIZETIER\PAGE\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * toutes les noisettes d'une page ou d'un objet.
 * Les compositions de page explicites ou virtuelles (créée par le noizetier) sont aussi prises en compte.
 *
 * Cette action est réservée aux utilisateurs autorisés.
 * Elle nécessite des arguments dont la liste dépend du contexte.
 *
 * @return void
 */
function action_vider_page_dist() {

	// Sécurisation et autorisation.
	// Les arguments attendus dépendent du contexte et la chaine peut prendre les formes suivantes:
	// - page:id_page, pour supprimer toutes les noisettes d'une page (y compris les compositions)
	// - objet:id_objet:type_objet, pour supprimer toutes les noisettes d'un objet précis
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	if ($arguments) {
		// On construit le tableau $options pour l'autorisation et on détermine l'invalideur.
		// L'identifiant de la page ou de l'objet se reconnait par le nombre d'éléments.
		$identifiants = explode(':', $arguments);
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

		// On récupère la liste des blocs ayant des noisettes
		if ($options['page']) {
			include_spip('inc/noizetier_page');
			$blocs = page_noizetier_compter_noisettes($options['page']);
			$page_ou_objet = $options['page'];
		} else {
			include_spip('inc/noizetier_objet');
			$blocs = objet_noizetier_compter_noisettes($options['objet'], $options['id_objet']);
			$page_ou_objet = $options;
		}

		// Suppression des noisettes concernées en utilisant l'API de vidage d'un conteneur, le conteneur étant
		// chaque bloc de la page ou de l'objet contenant des noisettes.
		if ($blocs) {
			include_spip('inc/ncore_conteneur');
			include_spip('inc/noizetier_conteneur');
			foreach (array_keys($blocs) as $_bloc) {
				// On calcule le conteneur sous sa forme identifiant chaine.
				$id_conteneur = conteneur_noizetier_composer($page_ou_objet, $_bloc);
				conteneur_vider('noizetier', $id_conteneur);
			}
		}

		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur($invalideur);
	}
}
