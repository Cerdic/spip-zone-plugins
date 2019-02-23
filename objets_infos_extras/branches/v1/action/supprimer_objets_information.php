<?php
/**
 * Utilisation de l'action supprimer pour l'objet objets_information
 *
 * @plugin     Infos extras pour objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Objets_infos_extras\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e objets_information
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, objets_information, #ID_OBJETS_INFORMATION}|oui)
 *         [(#BOUTON_ACTION{<:objets_information:supprimer_objets_information:>,
 *             #URL_ACTION_AUTEUR{supprimer_objets_information, #ID_OBJETS_INFORMATION, #URL_ECRIRE{objets_informations}},
 *             danger, <:objets_information:confirmer_supprimer_objets_information:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, objets_information, #ID_OBJETS_INFORMATION}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{objets_information-del-24.png}|balise_img{<:objets_information:supprimer_objets_information:>}|concat{' ',#VAL{<:objets_information:supprimer_objets_information:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_objets_information, #ID_OBJETS_INFORMATION, #URL_ECRIRE{objets_informations}},
 *             icone s24 horizontale danger objets_information-del-24, <:objets_information:confirmer_supprimer_objets_information:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'objets_information', $id_objets_information)) {
 *          $supprimer_objets_information = charger_fonction('supprimer_objets_information', 'action');
 *          $supprimer_objets_information($id_objets_information);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_objets_information_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_objets_informations',  'id_objets_information=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_objets_information_dist $arg pas compris");
	}
}
