<?php
/**
 * Action du plugin Coordonnée : associer un numero à un objet
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer un numero à un objet editorial
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{associer_numero, #ID_NUMERO/#OBJET/#ID_OBJET, #SELF}
 *     ```
 *
 * @param $arg string
 *     arguments séparés par un charactère non alphanumérique
 *
 *     - id_numero : identifiant de l'numero
 *     - objet : type d'objet à associer
 *     - id_objet : identifiant de l'objet à associer
 */
function action_associer_numero_dist($arg){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (
		list($id_numero, $objet, $id_objet) = preg_split('/\W/', $arg)
		AND intval($id_numero)>0 AND intval($id_objet)>0
		AND autoriser('modifier', $objet, $id_objet)
	){
		include_spip('action/editer_liens');
		objet_associer(array('numero' => $id_numero), array($objet => $id_objet));
	}
}

?>

