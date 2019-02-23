<?php
/**
 * Utilisation de l'action supprimer pour l'objet periode
 *
 * @plugin     Périodes
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Periodes\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e periode
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, periode, #ID_PERIODE}|oui)
 *         [(#BOUTON_ACTION{<:periode:supprimer_periode:>,
 *             #URL_ACTION_AUTEUR{supprimer_periode, #ID_PERIODE, #URL_ECRIRE{periodes}},
 *             danger, <:periode:confirmer_supprimer_periode:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, periode, #ID_PERIODE}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{periode-del-24.png}|balise_img{<:periode:supprimer_periode:>}|concat{' ',#VAL{<:periode:supprimer_periode:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_periode, #ID_PERIODE, #URL_ECRIRE{periodes}},
 *             icone s24 horizontale danger periode-del-24, <:periode:confirmer_supprimer_periode:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'periode', $id_periode)) {
 *          $supprimer_periode = charger_fonction('supprimer_periode', 'action');
 *          $supprimer_periode($id_periode);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_periode_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_periodes',  'id_periode=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_periode_dist $arg pas compris");
	}
}
