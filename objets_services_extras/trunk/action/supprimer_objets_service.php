<?php
/**
 * Utilisation de l'action supprimer pour l'objet objets_service
 *
 * @plugin     Services extras pour objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Objets_services_extras\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e objets_service
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, objets_service, #ID_OBJETS_SERVICE}|oui)
 *         [(#BOUTON_ACTION{<:objets_service:supprimer_objets_service:>,
 *             #URL_ACTION_AUTEUR{supprimer_objets_service, #ID_OBJETS_SERVICE, #URL_ECRIRE{objets_services}},
 *             danger, <:objets_service:confirmer_supprimer_objets_service:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, objets_service, #ID_OBJETS_SERVICE}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{objets_service-del-24.png}|balise_img{<:objets_service:supprimer_objets_service:>}|concat{' ',#VAL{<:objets_service:supprimer_objets_service:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_objets_service, #ID_OBJETS_SERVICE, #URL_ECRIRE{objets_services}},
 *             icone s24 horizontale danger objets_service-del-24, <:objets_service:confirmer_supprimer_objets_service:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'objets_service', $id_objets_service)) {
 *          $supprimer_objets_service = charger_fonction('supprimer_objets_service', 'action');
 *          $supprimer_objets_service($id_objets_service);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_objets_service_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_objets_services',  'id_objets_service=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_objets_service_dist $arg pas compris");
	}
}
