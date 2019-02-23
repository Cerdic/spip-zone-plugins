<?php
/**
 * Utilisation de l'action supprimer pour l'objet immeuble
 *
 * @plugin     Immeubles
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Immeubles\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e immeuble
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, immeuble, #ID_IMMEUBLE}|oui)
 *         [(#BOUTON_ACTION{<:immeuble:supprimer_immeuble:>,
 *             #URL_ACTION_AUTEUR{supprimer_immeuble, #ID_IMMEUBLE, #URL_ECRIRE{immeubles}},
 *             danger, <:immeuble:confirmer_supprimer_immeuble:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, immeuble, #ID_IMMEUBLE}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{immeuble-del-24.png}|balise_img{<:immeuble:supprimer_immeuble:>}|concat{' ',#VAL{<:immeuble:supprimer_immeuble:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_immeuble, #ID_IMMEUBLE, #URL_ECRIRE{immeubles}},
 *             icone s24 horizontale danger immeuble-del-24, <:immeuble:confirmer_supprimer_immeuble:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'immeuble', $id_immeuble)) {
 *          $supprimer_immeuble = charger_fonction('supprimer_immeuble', 'action');
 *          $supprimer_immeuble($id_immeuble);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_immeuble_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_immeubles',  'id_immeuble=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_immeuble_dist $arg pas compris");
	}
}
