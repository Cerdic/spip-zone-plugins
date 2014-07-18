<?php
/**
 * Action : supprimer un album
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprime un album proprement
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{supprimer_album,#ID_ALBUM,#SELF}
 *     #URL_ACTION_AUTEUR{supprimer_album,#ID_ALBUM/orphelins,#SELF}
 *     ```
 *
 * @uses supprimer_albums()
 *
 * @param $arg string
 *     Arguments séparés par un charactère non alphanumérique
 *     sous la forme `$id_album/orphelins`
 *
 *     - id_album  : identifiant de l'album
 *     - orphelins : «orphelins» pour supprimer les documents rendus orphelins
 * @return void
 */
function action_supprimer_album_dist($arg=null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_album, $supprimer_orphelins) = preg_split('/\W/', $arg);
	$supprimer_docs_orphelins = ($supprimer_docs_orphelins=='orphelins') ? true : false;

	// suppression
	if ($id_album = intval($id_album)) {
		include_spip('inc/albums');
		supprimer_albums($id_album,$supprimer_docs_orphelins);
	}
}

?>
