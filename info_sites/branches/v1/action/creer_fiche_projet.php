<?php
/**
 * Créer une fiche de projet à partir d'une référence
 *
 * @plugin     InfoSites
 * @copyright  2017-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/Infosites/Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_creer_fiche_projet_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	if (intval($arg) > 0) {
		include_spip('base/abstract_sql');
		$reference = sql_fetsel('nom,url_site,organisation', 'spip_projets_references', 'id_projets_reference=' . $arg);
		$tmp_projet = sql_getfetsel('id_projet', 'spip_projets', "url_site='" . $reference['url_site'] . "'");
		$tmp_organisation = sql_getfetsel('id_organisation', 'spip_organisations', "nom='" . $reference['organisation'] . "'");

		if (intval($tmp_projet) == 0) {
			$id_projet = sql_insertq('spip_projets',
				array(
					'id_projet_parent' => 0,
					'nom' => $reference['nom'],
					'url_site' => $reference['url_site'],
					'id_projets_cadre' => 0,
					'date_publication' => date_format(date_create(), 'Y-m-d H:i:s'),
					'statut' => 'prepa',
				)
			);
			spip_log('Le projet #' . $id_projet . ' a été créé grâce à la référence de projets #' . $arg, 'info_sites');
			if (intval($tmp_organisation) == 0) {
				$id_organisation = sql_insertq('spip_organisations',
					array(
						'id_annuaire' => 0,
						'id_parent' => 0,
						'id_auteur' => 0,
						'nom' => $reference['organisation'],
						'date_creation' => date_format(date_create(), 'Y-m-d H:i:s'),
					)
				);
				spip_log('L\'organisation #' . $id_organisation . ' a été créé grâce à la référence de projets #' . $arg, 'info_sites');
				} else {
				$id_organisation = intval($tmp_organisation);
			}
			if (intval($id_organisation) > 0) {
				include_spip('action/editer_liens');
				objet_associer(array('projet' => $id_projet), array('organisation' => $id_organisation));
				spip_log('L\'organisation #' . $id_organisation . ' et le projet #' . $id_projet . ' ont été lié grâce à la référence de projets #' . $arg, 'info_sites');
				include_spip('inc/session');
				if ($id_auteur = session_get('id_auteur') and intval($id_auteur) > 0) {
					objet_associer(array('auteur' => intval($id_auteur)), array('projet' => $id_projet), array('vu' => 'non', 'role' => 'chef_projets'));
					spip_log('L\'auteur #' . $id_auteur . ' et le projet #' . $id_projet . ' ont été lié grâce à la référence de projets #' . $arg, 'info_sites');

				}
			}
		} else {
			spip_log('L\'url ' . $reference['url_site'] . ' est associé au projet #' . $tmp_projet . '. Donc, aucune création de fiche de projet n\'est réalisée.', 'info_sites');

		}
	}
}
