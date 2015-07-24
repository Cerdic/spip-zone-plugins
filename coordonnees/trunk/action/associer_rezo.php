<?php
/**
 * Action du plugin Coordonnée : associer un réseau social à un objet
 *
 * @plugin     coordonnées
 * @copyright  2015
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\coordonnees\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer un réseau social à un objet editorial
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{associer_rezo, #ID_REZO/#OBJET/#ID_OBJET, #SELF}
 *     ```
 *
 * @param $arg string
 *     arguments séparés par un charactère non alphanumérique
 *
 *     - id_rezo : identifiant du réseau social
 *     - objet : type d'objet à associer
 *     - id_objet : identifiant de l'objet à associer
 */
function action_associer_rezo_dist($arg){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (
		list($id_rezo, $objet, $id_objet) = preg_split('/\W/', $arg)
		AND intval($id_rezo)>0 AND intval($id_objet)>0
		AND autoriser('modifier', $objet, $id_objet)
	){
		include_spip('action/editer_liens');
		objet_associer(array('rezo' => $id_rezo), array($objet => $id_objet));
	}
}

?>

