<?php
/**
 * Utilisation de l'action supprimer pour l'objet restriction
 *
 * @plugin     Locations d&#039;objets - restrictions
 * @copyright  2019
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Locations_objets_restrictions\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e restriction
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, restriction, #ID_RESTRICTION}|oui)
 *         [(#BOUTON_ACTION{<:restriction:supprimer_restriction:>,
 *             #URL_ACTION_AUTEUR{supprimer_restriction, #ID_RESTRICTION, #URL_ECRIRE{restrictions}},
 *             danger, <:restriction:confirmer_supprimer_restriction:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, restriction, #ID_RESTRICTION}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{restriction-del-24.png}|balise_img{<:restriction:supprimer_restriction:>}|concat{' ',#VAL{<:restriction:supprimer_restriction:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_restriction, #ID_RESTRICTION, #URL_ECRIRE{restrictions}},
 *             icone s24 horizontale danger restriction-del-24, <:restriction:confirmer_supprimer_restriction:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'restriction', $id_restriction)) {
 *          $supprimer_restriction = charger_fonction('supprimer_restriction', 'action');
 *          $supprimer_restriction($id_restriction);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_restriction_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_restrictions',  'id_restriction=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_restriction_dist $arg pas compris");
	}
}
