<?php
/**
 * Action : dissocier un ou tous les albums liés à un objet éditorial
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
 * Dissocier un ou tous les albums liés à un objet éditorial
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{dissocier_album, #ID_ALBUM/#OBJET/#ID_OBJET, #SELF}
 *     #URL_ACTION_AUTEUR{dissocier_album, tous/#OBJET/#ID_OBJET, #SELF}
 *     ```
 *
 * @param string $arg
 *     Arguments séparés par un slash «/»
 *     sous la forme `$album/$objet/$id_objet`
 *
 *     - album      : identifiant d'un album pour dissocier un album unique
 *                    «tous» pour dissocier tous les albums
 *     - objet      : type d'objet à dissocier
 *     - id_objet   : identifiant de l'objet à dissocier
 * @return void
 */
function action_dissocier_album_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($album, $objet, $id_objet) = explode('/', $arg);

	// si l'identifiant de l'objet est négatif, vérifier qu'il correspondant à celui du visiteur,
	// (cas d'un album lié à un objet pas encore enregistré en base).
	if (
		$id_objet = intval($id_objet)
		AND (
			($id_objet<0 AND $id_objet==-$GLOBALS['visiteur_session']['id_auteur'])
			OR autoriser('modifier',$objet,$id_objet)
		)
	) {
		include_spip('action/editer_liens');
		switch ($album) {
			case 'tous' :
				// Ne dissocier que les albums non insérés dans le texte.
				// = autorisation à dissocier un album d'un objet,
				// sauf qu'on économise des requêtes.
				if (is_array($liens = objet_trouver_liens(array('album'=>'*'),array($objet=>$id_objet)))){
					foreach($liens as $lien) {
						if ($lien['vu'] == 'non')
							$ids_albums[] = $lien['id_album'];
					}
					objet_dissocier(array('album'=>$ids_albums),array($objet=>$id_objet));
				}
				break;
			default :
				if (
					$id_album = intval($album)
					AND autoriser('dissocier','album',$id_album,'',array('objet'=>$objet,'id_objet'=>$id_objet))
				){
					objet_dissocier(array('album'=>$id_album),array($objet=>$id_objet));
				}
				break;
		}
	}
}

?>
