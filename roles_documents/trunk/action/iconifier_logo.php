<?php
/**
 * Action : Convertir un vieux logo en document
 *
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Roles_documents\Action
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Convertir un vieux logo en document avec le rôle de logo
 *
 * @param $arg string
 *     Arguments séparés par un tiret
 *     sous la forme `$objet-$id_objet-$etat`
 *
 *     - objet     : type d'objet
 *     - id_objet  : identifiant de l'objet
 *     - etat      : on ou off
 * @return void
 */
function action_iconifier_logo_dist($arg = null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		//$arg = $_REQUEST['arg'];
	}
	list($objet, $id_objet, $etat) = explode('-', $arg);

	include_spip('inc/autoriser');
	if (autoriser('iconifier', $objet, $id_objet)) {

		include_spip('base/objets');

		// Chercher le logo : la fonction renvoie en priorité les vieux logos
		$id_table_objet = id_table_objet($objet);
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if ($logo = $chercher_logo($id_objet, $id_table_objet, $etat)) {

			// Ajouter le logo en tant que document
			$_files = array(
				array(
					'tmp_name' => $logo[0],
					'name'     => @pathinfo($logo[0], PATHINFO_BASENAME),
					'mode'     => 'image',
					'type'     => @mime_content_type($logo[0]),
				)
			);
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');
			if ($ids_documents = $ajouter_documents('new', $_files, $objet, $id_objet, 'image')
				and $id_document = intval($ids_documents[0])
			) {

				// Retrouver le rôle de logo à attribuer
				$etats_roles = array(
					'on'  => 'logo',
					'off' => 'logo_survol',
				);
				$role = $etats_roles[$etat];

				// Créer ou mettre à jour le lien existant
				// Si un lien existe déjà, on change l'id_document
				if (sql_countsel(
					'spip_documents_liens',
					array(
						'objet=' . sql_quote($objet),
						'id_objet=' . intval($id_objet),
						'role=' . sql_quote($role),
					)
				)) {
					$update = sql_updateq(
						'spip_documents_liens',
						array(
							'id_document' => intval($id_document),
						),
						array(
							'objet=' . sql_quote($objet),
							'id_objet=' . intval($id_objet),
							'role=' . sql_quote($role)
						)
					);

				// Sinon on crée un nouveau lien
				} else {
					$insert = sql_insertq(
						'spip_documents_liens',
						array(
							'id_document' => intval($id_document),
							'objet'       => $objet,
							'id_objet'    => intval($id_objet),
							'role'        => $role,
						)
					);
				}

				// On supprime l'ancien logo
				include_spip('action/editer_logo');
				logo_supprimer($objet, $id_objet, $etat);

			}
		}
	}
}
