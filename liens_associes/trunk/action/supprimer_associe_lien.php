<?php
/**
 * Utilisation de l'action supprimer pour l'objet associe_lien
 *
 * @plugin     Liens associés
 * @copyright  2017
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Liens_associes\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e associe_lien
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, associe_lien, #ID_ASSOCIE_LIEN}|oui)
 *         [(#BOUTON_ACTION{<:associe_lien:supprimer_associe_lien:>,
 *             #URL_ACTION_AUTEUR{supprimer_associe_lien, #ID_ASSOCIE_LIEN, #URL_ECRIRE{associe_liens}},
 *             danger, <:associe_lien:confirmer_supprimer_associe_lien:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, associe_lien, #ID_ASSOCIE_LIEN}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{associe_lien-del-24.png}|balise_img{<:associe_lien:supprimer_associe_lien:>}|concat{' ',#VAL{<:associe_lien:supprimer_associe_lien:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_associe_lien, #ID_ASSOCIE_LIEN, #URL_ECRIRE{associe_liens}},
 *             icone s24 horizontale danger associe_lien-del-24, <:associe_lien:confirmer_supprimer_associe_lien:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'associe_lien', $id_associe_lien)) {
 *          $supprimer_associe_lien = charger_fonction('supprimer_associe_lien', 'action');
 *          $supprimer_associe_lien($id_associe_lien);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_associe_lien_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_associe_liens',  'id_associe_lien=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_associe_lien_dist $arg pas compris");
	}
}
