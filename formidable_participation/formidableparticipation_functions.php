<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'insertion de participant dans l'agenda
 *
 * @param mixed $c tableau des champs
 * @access public
 */
function formidableparticipation_inserer($c) {

	// Définir le retour du traitement
	$retours = array();

	$id_evenement = $c['id_evenement'];
	$reponse = $c['choix_participation'];
	$email = $c['email'];
	$id_auteur = $c['id_auteur'];
	$nom = $c['nom'];
	$prenom = $c['prenom'];

	if ($c['organisme']) {
		$organisme = '('.$c['organisme'].')';
	}

	$nom = trim("$prenom $nom $organisme");

	$champs = array(
		'id_auteur' => $id_auteur,
		'nom' => $nom,
		'email' => $email,
		'reponse' => $reponse,
		'id_evenement' => $id_evenement,
		'date' => date('Y-m-d H:i:s')
	);

	// si evenement, on insere le participant et ses données
	// et on laisse le traitement du nombre de places à la charge du webmestre et du squelette evenements
	if (isset($id_evenement)) {

		// Respecter le nombre de places disponibles
		$places = sql_getfetsel('places', 'spip_evenements', 'id_evenement='.intval($id_evenement));
		$nb_participant = sql_countsel('spip_evenements_participants', 'id_evenement='.intval($id_evenement));

		if ($nb_participant >= $places) {
			$retours['message_erreur'] = _T('agenda:plus_de_place');
			return $retours;
		}

		// on ne loge pas l'auteur, si l'email sur le même id_evenement existe, mettre à jour
		$reponse = sql_fetsel(
			'reponse',
			'spip_evenements_participants',
			'id_evenement='.intval($id_evenement).'
                and email='.sql_quote($email)
		);

		if (!is_null($reponse)) {
			sql_updateq(
				'spip_evenements_participants',
				$champs,
				'id_evenement='.intval($id_evenement).'
                    and email='.sql_quote($email)
			);
		} else {
			sql_insertq('spip_evenements_participants', $champs);
		}

		// Message OK
		$retours['message_ok'] = _T('agenda:participation_prise_en_compte');
	}

	spip_log(
		"pipeline evenement $id_evenement pour $email et id_auteur=$id_auteur reponse=$reponse",
		'formidable_participation'
	);

	return $retours;
}
