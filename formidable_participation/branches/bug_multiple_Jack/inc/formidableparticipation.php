<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Détermine au moment de le saisie du formulaire si la personne participe ou pas
 * @param array $options les options du traitement participation
 * @return str oui|non
 **/
function formidableparticipation_choix_participation($options) {
	if ($options['participation_auto'] == 'auto') {
		$choix_participation = 'oui';
	} elseif ($options['champ_choix_participation']) {
		$choix_participation = _request($options['champ_choix_participation']);

		if ($options['choix_participation_oui']) {
			$participation_oui = $options['choix_participation_oui'];
		}

		if($choix_participation == $participation_oui) {
			$choix_participation='oui';
		} else {
			$choix_participation='non';
		}
	}
	return $choix_participation;
}


/**
 * Détermine au moment de le saisie du formulaire si la personne participe ou pas
 * @param array $options les options du traitement participation
 * @return array tableau des $id_evenement
 **/
function formidableparticipation_id_evenement($options) {
	if ($options['evenement_type'] == 'fixe') {
		$id_evenement = $options['id_evenement_participation'];
	} elseif ($options['evenement_type'] == 'variable' and isset($options['champ_evenement_participation'])) {
		$id_evenement = _request($options['champ_evenement_participation']);
	}
	if (!is_array($id_evenement)) {
		$id_evenement = array($id_evenement);
	}
	return $id_evenement;
}
