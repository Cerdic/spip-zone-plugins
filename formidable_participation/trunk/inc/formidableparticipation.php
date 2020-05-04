<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('formidable_fonctions');
/**
 * Détermine au moment de le saisie du formulaire si la personne participe ou pas
 * @param int|val $id_formulaire
 * @param int|val $id_formulaires_reponse
 * @param array $options les options du traitement participation
 * @return str oui|non
 **/
function formidableparticipation_choix_participation($id_formulaire, $id_formulaires_reponse, $options) {
	if ($options['participation_auto'] == 'auto') {
		$choix_participation = 'oui';
	} elseif ($options['champ_choix_participation']) {
		$choix_participation =
			calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $options['champ_choix_participation'], '', 'brut', '');
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
 * @param int|val $id_formulaire
 * @param int|val $id_formulaires_reponse
 * @param array $options les options du traitement participation
 * @return array tableau des $id_evenement
 **/
function formidableparticipation_id_evenement($id_formulaire, $id_formulaires_reponse, $options) {
	if ($options['evenement_type'] == 'fixe') {
		$id_evenement = $options['id_evenement_participation'];
	} elseif ($options['evenement_type'] == 'variable' and isset($options['champ_evenement_participation'])) {
		$id_evenement = calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $options['champ_evenement_participation'], '', 'brut', '');
	}
	if (!is_array($id_evenement)) {
		$id_evenement = array($id_evenement);
	}
	return $id_evenement;
}
