<?php
/**
 * Utilisation de l'action supprimer pour l'objet svg
 *
 * @plugin     SVG en base de données
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Svgbase\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e svg
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, svg, #ID_SVG}|oui)
 *         [(#BOUTON_ACTION{<:svg:supprimer_svg:>,
 *             #URL_ACTION_AUTEUR{supprimer_svg, #ID_SVG, #URL_ECRIRE{svg}},
 *             danger, <:svg:confirmer_supprimer_svg:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, svg, #ID_SVG}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{svg-del-24.png}|balise_img{<:svg:supprimer_svg:>}|concat{' ',#VAL{<:svg:supprimer_svg:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_svg, #ID_SVG, #URL_ECRIRE{svg}},
 *             icone s24 horizontale danger svg-del-24, <:svg:confirmer_supprimer_svg:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'svg', $id_svg)) {
 *          $supprimer_svg = charger_fonction('supprimer_svg', 'action');
 *          $supprimer_svg($id_svg);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_svg_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_svg',  'id_svg=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_svg_dist $arg pas compris");
	}
}
