<?php
/**
 * Utilisation de l'action supprimer pour l'objet disponibilite_date
 *
 * @plugin     Disponibilites objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_disponibilites\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e disponibilite_date
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, disponibilite_date, #ID_DISPONIBILITE_DATE}|oui)
 *         [(#BOUTON_ACTION{<:disponibilite_date:supprimer_disponibilite_date:>,
 *             #URL_ACTION_AUTEUR{supprimer_disponibilite_date, #ID_DISPONIBILITE_DATE, #URL_ECRIRE{disponibilite_dates}},
 *             danger, <:disponibilite_date:confirmer_supprimer_disponibilite_date:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, disponibilite_date, #ID_DISPONIBILITE_DATE}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{disponibilite_date-del-24.png}|balise_img{<:disponibilite_date:supprimer_disponibilite_date:>}|concat{' ',#VAL{<:disponibilite_date:supprimer_disponibilite_date:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_disponibilite_date, #ID_DISPONIBILITE_DATE, #URL_ECRIRE{disponibilite_dates}},
 *             icone s24 horizontale danger disponibilite_date-del-24, <:disponibilite_date:confirmer_supprimer_disponibilite_date:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'disponibilite_date', $id_disponibilite_date)) {
 *          $supprimer_disponibilite_date = charger_fonction('supprimer_disponibilite_date', 'action');
 *          $supprimer_disponibilite_date($id_disponibilite_date);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_disponibilite_date_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_disponibilite_dates',  'id_disponibilite_date=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_disponibilite_date_dist $arg pas compris");
	}
}
