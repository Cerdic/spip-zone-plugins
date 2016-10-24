<?php
/**
 * Action : dissocier un ou tous les encarts liés à un objet éditorial
 *
 * @plugin     Encarts
 * @copyright  2013-2016
 * @noteauthor Cloné à partir du fichier similaire du plugin Albums
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Dissocier un ou tous les encarts liés à un objet éditorial
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{dissocier_encart, #ID_ENCARTS/#OBJET/#ID_OBJET, #SELF}
 *     #URL_ACTION_AUTEUR{dissocier_encart, tous/#OBJET/#ID_OBJET, #SELF}
 *     ```
 *
 * @param string $arg
 *     Arguments séparés par un slash «/»
 *     sous la forme `$encart/$objet/$id_objet`
 *
 *     - encart      : identifiant d'un encart pour dissocier uniquement cet encart
 *                    «tous» pour dissocier tous les encarts
 *     - objet      : type d'objet à dissocier
 *     - id_objet   : identifiant de l'objet à dissocier
 * @return void
 */
function action_dissocier_encart_dist($arg = null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($encart, $objet, $id_objet) = explode('/', $arg);

	// si l'identifiant de l'objet est négatif, vérifier qu'il correspond à celui du visiteur,
	// (cas d'un encart lié à un objet pas encore enregistré en base).
	if (
		$id_objet = intval($id_objet)
		AND (
			($id_objet < 0 AND $id_objet == -$GLOBALS['visiteur_session']['id_auteur'])
			OR autoriser('modifier', $objet, $id_objet)
		)
	) {
		include_spip('action/editer_liens');
		switch ($encart) {
			case 'tous' :
				// Ne dissocier que les encarts non insérés dans le texte.
				// = autorisation à dissocier un encart d'un objet,
				// sauf qu'on économise des requêtes.
				if (is_array($liens = objet_trouver_liens(array('encart' => '*'), array($objet => $id_objet)))) {
					foreach ($liens as $lien) {
						if ($lien['vu'] == 'non') {
							$ids_encarts[] = $lien['id_encart'];
						}
					}
					objet_dissocier(array('encart' => $ids_encarts), array($objet => $id_objet));
				}
				break;
			default :
				if (
					$id_encart = intval($encart)
					AND autoriser('dissocier', 'encart', $id_encart, '', array(
						'objet' => $objet,
						'id_objet' => $id_objet
					))
				) {
					objet_dissocier(array('encart' => $id_encart), array($objet => $id_objet));
				}
				break;
		}
	}
}

?>
