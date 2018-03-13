<?php
/**
 * Gestion de l'action qui supprime un encart publicitaire
 *
 * @plugin     Encarts
 * @copyright  2012-2018
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Action
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Supprimer un encart publicitaire
 *
 * @note
 * L'encart doit être vide (pas de campagnes publicitaires associées)
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{supprimer_encart,#ID_ENCART,#SELF}
 *     ```
 *
 * @uses objet_supprimer()
 *
 * @param $arg string
 *     Identifiant de l'encart
 * @return void
 */
function action_supprimer_encart_dist($arg = null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	include_spip('inc/autoriser');
	if ($id_encart = intval($arg)
		and autoriser('supprimer', 'encart', $id_encart)
	) {

		// Suppression
		sql_delete('spip_encarts', array('id_encart = ' . $id_encart));

		// Invalidation des caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='encart/$id_encart'");
	}

}
