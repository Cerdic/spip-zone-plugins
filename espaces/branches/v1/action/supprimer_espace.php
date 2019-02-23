<?php
/**
 * Utilisation de l'action supprimer pour l'objet espace
 *
 * @plugin     Espaces
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Espaces\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
  return;
}



/**
 * Action pour supprimer un·e espace
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, espace, #ID_ESPACE}|oui)
 *         [(#BOUTON_ACTION{<:espace:supprimer_espace:>,
 *             #URL_ACTION_AUTEUR{supprimer_espace, #ID_ESPACE, #URL_ECRIRE{espaces}},
 *             danger, <:espace:confirmer_supprimer_espace:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, espace, #ID_ESPACE}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{espace-del-24.png}|balise_img{<:espace:supprimer_espace:>}|concat{' ',#VAL{<:espace:supprimer_espace:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_espace, #ID_ESPACE, #URL_ECRIRE{espaces}},
 *             icone s24 horizontale danger espace-del-24, <:espace:confirmer_supprimer_espace:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'espace', $id_espace)) {
 *          $supprimer_espace = charger_fonction('supprimer_espace', 'action');
 *          $supprimer_espace($id_espace);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_espace_dist($arg=null) {
  if (is_null($arg)){
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
  }
  $arg = intval($arg);

  // cas suppression
  if ($arg) {
    sql_delete('spip_espaces',  'id_espace=' . sql_quote($arg));
  }
  else {
    spip_log("action_supprimer_espace_dist $arg pas compris");
  }
}
