<?php
/**
 * Traitement participation à la saisie d'un formulaire
 *
 * @plugin     Formulaires de participation
 * @copyright  2014
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Formidableparticipation\traiter\participation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_participation_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);

	// saisies dans le formulaire
	if ($options['champ_choix_participation']) {
		$choix_participation = _request($options['champ_choix_participation']);
	}

	if ($options['champ_email_participation']) {
		$email_participation = _request($options['champ_email_participation']);
	}

	if ($options['champ_nom_participation']) {
		$nom_participation = _request($options['champ_nom_participation']);
	}

	if ($options['champ_prenom_participation']) {
		$prenom_participation = _request($options['champ_prenom_participation']);
	}

	if ($options['champ_organisme_participation']) {
		$organisme_participation = _request($options['champ_organisme_participation']);
	}

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
	$id_formulaires_reponse = $retours['id_formulaires_reponse'];


	// Si la réponse n'est pas publié (modération a priori), alors la réponse est non, en attendant une éventuelle future publication
	$statut_reponse = sql_getfetsel('statut', 'spip_formulaires_reponses', "id_formulaires_reponse=$id_formulaires_reponse");
	if ($statut_reponse != 'publie') {
		$choix_participation = 'non';
	}

	// détermination de l'évènement où s'inscrire
	if ($options['evenement_type'] == 'fixe') {
		$id_evenement = $options['id_evenement_participation'];
	} elseif ($options['evenement_type'] == 'variable' and isset($options['champ_evenement_participation'])) {
		$id_evenement = _request($options['champ_evenement_participation']);
	}

	// Nombre total d'inscription
	if (isset($options['plusieurs_fois']) and $options['plusieurs_fois']) {
		$nb_inscriptions = 0;
		foreach ($options['champ_nb_inscriptions'] as $champ) {
			$nb_inscriptions = $nb_inscriptions + _request($champ);
		}
	} else {
		$nb_inscriptions = 1;
	}
	if (!is_array($id_evenement)) {
		$id_evenement = array($id_evenement);
	}
	foreach ($id_evenement as $evenement) {
		$options = array(
			'id_evenement'=> $evenement, //si oui, traitement avec agenda
			'choix_participation' => $choix_participation,
			'email' => $email_participation,
			'champ_evenement_participation' => $options['champ_evenement_participation'],
			'autoriser_email_multiple' => $options['autoriser_email_multiple'],
			'nom' => $nom_participation,
			'prenom' => $prenom_participation,
			'organisme' => $organisme_participation,
			'nb_inscriptions' => $nb_inscriptions,
			'id_auteur' => (isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:0),
			'parrain' => 'form'.$formulaire['id_formulaire'].':'.$formulaire['identifiant'],
			'tracking_id' => $id_formulaires_reponse,//Garder pour des raisons historiques, même si apparement jamais servi
			'id_formulaires_reponse' => $id_formulaires_reponse
		);
		// fabrique le pipeline traiter_formidableparticipation.
		$pipeline = pipeline('traiter_formidableparticipation',array('args'=>$options,'data'=>$pipeline));
	}

	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['participation'] = true;

	return $retours;
}
