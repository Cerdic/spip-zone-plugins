<?php
/**
 * Utilisation de l'action supprimer pour l'objet objets_location
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e objets_location
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, objets_location, #ID_OBJETS_LOCATION}|oui)
 *         [(#BOUTON_ACTION{<:objets_location:supprimer_objets_location:>,
 *             #URL_ACTION_AUTEUR{supprimer_objets_location, #ID_OBJETS_LOCATION, #URL_ECRIRE{objets_locations}},
 *             danger, <:objets_location:confirmer_supprimer_objets_location:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, objets_location, #ID_OBJETS_LOCATION}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{objets_location-del-24.png}|balise_img{<:objets_location:supprimer_objets_location:>}|concat{' ',#VAL{<:objets_location:supprimer_objets_location:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_objets_location, #ID_OBJETS_LOCATION, #URL_ECRIRE{objets_locations}},
 *             icone s24 horizontale danger objets_location-del-24, <:objets_location:confirmer_supprimer_objets_location:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'objets_location', $id_objets_location)) {
 *          $supprimer_objets_location = charger_fonction('supprimer_objets_location', 'action');
 *          $supprimer_objets_location($id_objets_location);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_objets_location_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_objets_locations',  'id_objets_location=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_objets_location_dist $arg pas compris");
	}
}
