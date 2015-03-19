<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Action de suppression d'une ressoruce
 *
 * @package SPIP\Orr\Ressoruce 
**/

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Effacer une ressource
 *
 * @param null|int $id_orr_ressource
 * @return void
 */
function action_supprimer_orr_ressource_dist($id_orr_ressource=null) {

	if (is_null($id_orr_ressource)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_orr_ressource = $securiser_action();
	}

	if (intval($id_orr_ressource)){

		include_spip('inc/autoriser');

		if (!autoriser('supprimer', 'orr_ressource', $id_orr_ressource)) {
			include_spip('inc/minipres');
			echo minipres(_T('info_acces_interdit'));
			die();
		}

		include_spip('action/editer_liens');

		objet_dissocier(array('orr_reservation'=>'*'), array('orr_ressource'=>$id_orr_ressource));

		sql_delete("spip_orr_ressources", "id_orr_ressource=".intval($id_orr_ressource));

		// supprimer toutes les réservations qui n'ont pas de ressources liées.
		$ids_reservations_vides = sql_allfetsel(
			'DISTINCT(orr.id_orr_reservation)',
			'spip_orr_reservations AS orr LEFT OUTER JOIN spip_orr_reservations_liens AS orl ON (orr.id_orr_reservation = orl.id_orr_reservation)',
			'orl.id_objet IS NULL'
		);

		if ($ids_reservations_vides) {
			$ids_reservations_vides = array_map('array_shift', $ids_reservations_vides);
			sql_delete("spip_orr_reservations", sql_in("id_orr_reservation", $ids_reservations_vides));
		}

	}

}
