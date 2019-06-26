<?php
/**
 * Utilisation de l'action supprimer pour l'objet materialicon
 *
 * @plugin     Material Icônes
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Materialicons\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e materialicon
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, materialicon, #ID_MATERIALICON}|oui)
 *         [(#BOUTON_ACTION{<:materialicon:supprimer_materialicon:>,
 *             #URL_ACTION_AUTEUR{supprimer_materialicon, #ID_MATERIALICON, #URL_ECRIRE{materialicons}},
 *             danger, <:materialicon:confirmer_supprimer_materialicon:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, materialicon, #ID_MATERIALICON}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{materialicon-del-24.png}|balise_img{<:materialicon:supprimer_materialicon:>}|concat{' ',#VAL{<:materialicon:supprimer_materialicon:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_materialicon, #ID_MATERIALICON, #URL_ECRIRE{materialicons}},
 *             icone s24 horizontale danger materialicon-del-24, <:materialicon:confirmer_supprimer_materialicon:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'materialicon', $id_materialicon)) {
 *          $supprimer_materialicon = charger_fonction('supprimer_materialicon', 'action');
 *          $supprimer_materialicon($id_materialicon);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_materialicon_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_materialicons',  'id_materialicon=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_materialicon_dist $arg pas compris");
	}
}
