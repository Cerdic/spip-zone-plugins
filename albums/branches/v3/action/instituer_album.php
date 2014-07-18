<?php
/**
 * Action : changer le statut d'un album
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
 * Changer le statut d'un album
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{instituer_album, #ID_ALBUM/publie, #SELF}
 *     ```
 *
 * @param string $arg
 *     Arguments séparés par un charactère non alphanumérique
 *     sous la forme `$id_album/$statut`
 *
 *     - id_album : identifiant de l'album
 *     - statut   : nouveau statut (prepa|publie|poubelle)
 * @return void
 */
function action_instituer_album_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_album, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // sait-on jamais

	if ($id_album = intval($id_album)) {
		include_spip('action/editer_objet');
		objet_instituer('album', $id_album, array('statut'=>$statut),false);
	}

}


?>
