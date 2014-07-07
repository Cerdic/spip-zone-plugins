<?php
/**
 * Action : associer un album à un objet éditorial
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GPL
 * @package    SPIP\Albums\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer un album à un objet éditorial
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{associer_album, #ID_ALBUM/#OBJET/#ID_OBJET, #SELF}
 *     ```
 *
 * @param string $arg
 *     Arguments séparés par un charactère non alphanumérique
 *     sous la forme `$id_album/$objet/$id_objet`
 *
 *     - id_album : identifiant de l'album
 *     - objet : type d'objet à associer
 *     - id_objet : identifiant de l'objet à associer
 * @return void
 */
function action_associer_album_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_album, $objet, $id_objet) = preg_split('/\W/', $arg);

	if ($id_album = intval($id_album)){
		include_spip('action/editer_liens');
		objet_associer(array('album'=>$id_album), array($objet=>$id_objet));
	}
}

?>
