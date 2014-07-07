<?php
/**
 * Action : «vider» un album en dissociant tous ses documents.
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
 * «Vider» un album : dissocier tous ses documents
 *
 * Optionnellement, on peut supprimer les documents rendus orphelins,
 * ainsi que l'album.
 *
 * @note
 * Impossible de retirer en une fois tous les documents d'un album
 * via le bouton d'action `dissocier_document` du plugin médias.
 * Il faut lui passer en paramètre un des 3 «modes» pour les documents :
 *
 * - les images en mode Image : `I/image`
 * - les images en mode document : `D/image`
 * - les documents non image en mode document : 'D/document'
 * 
 * Cf. fonction `dissocier_document` dans `action/dissocier_document.php`.
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{vider_album, #ID_ALBUM, #SELF}
 *     ```
 *
 * @uses vider_albums()
 *
 * @param $arg string
 *     Arguments séparés par un charactère non alphanumérique
 *     sous la forme `$id_album-supprimer-orphelins`
 *
 *     - id_album  : identifiant de l'album
 *     - orphelins : pour supprimer les documents rendus orphelins
 *     - supprimer : pour supprimer l'album à la fin de l'opération
 * @return void
 */
function action_vider_album_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_album, $supprimer_orphelins, $supprimer_album) = preg_split('/\W/', $arg);
	$supprimer_orphelins = ($supprimer_orphelins=='orphelins') ? true : false;
	$supprimer_album = ($supprimer_album=='supprimer') ? true : false;

	if ($id_album = intval($id_album)) {
		include_spip('inc/albums');
		vider_albums($id_album,$supprimer_orphelins);
	}
}

?>
