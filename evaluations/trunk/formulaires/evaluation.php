<?php
/**
 * Gestion du formulaire d'evaluation
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/evaluations');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_evaluation
 *     Identifiant du evaluation. 'new' pour un nouveau evaluation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $objet
 *     Type d'objet sur quoi porte l'évaluation
 * @param int $id_objet
 *     Identifiant d'objet sur quoi porte l'évaluation
 * @return string
 *     Hash du formulaire
 */
function formulaires_evaluation_identifier_dist($id_evaluation=0, $retour='', $objet='', $id_objet=0){
	return md5(serialize(intval($id_evaluation).$objet.$id_objet));
}

/**
 * Chargement du formulaire d'évaluation
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param int|string $identifiant
 *     Identifiant (numérique ou textuel) de l'évaluation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $objet
 *     Type d'objet sur quoi porte l'évaluation
 * @param int $id_objet
 *     Identifiant d'objet sur quoi porte l'évaluation
 * @return array
 *     Environnement du formulaire
 */
function formulaires_evaluation_charger_dist($identifiant=0, $retour='', $objet='', $id_objet=0){
	$valeurs = array();

	$id_evaluation = evaluations_obtenir_identifiant($identifiant);

	include_spip('inc/autoriser');
	if (!autoriser('evaluer', 'evaluation', $id_evaluation)) {
		return null;
	}

	$valeurs['id_evaluation'] = $id_evaluation;
	$valeurs['objet']         = $objet;
	$valeurs['id_objet']      = $id_objet;
	$valeurs['critere']       = array();
	$valeurs['date_critique'] = ''; // date de critique de l'auteur, s'il a déjà répondu

	// si l'auteur a déjà voté pour cet objet et identifiant
	// on lui remet ce qu'il avait déjà enregistré.
	include_spip('inc/session');
	$id_auteur = session_get('id_auteur');

	if ($critiques = sql_allfetsel('*', 'spip_evaluations_critiques', array(
		'id_evaluation = ' . sql_quote($id_evaluation),
		'objet = '         . sql_quote($objet),
		'id_objet = '      . sql_quote($id_objet),
		'id_auteur = '     . sql_quote($id_auteur),
	))) {
		foreach ($critiques as $c) {
			$critere = $c['id_evaluations_critere'];
			$vals = array_intersect_key($c, array_flip(array('note', 'commentaire', 'forces', 'faiblesses')));
			$valeurs['critere'][$critere] = $vals;
		}
		$valeurs['date_critique'] = $c['date'];
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'évaluation
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @param int|string $identifiant
 *     Identifiant de l'évaluation.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $objet
 *     Type d'objet sur quoi porte l'évaluation
 * @param int $id_objet
 *     Identifiant d'objet sur quoi porte l'évaluation
 * @return array
 *     Tableau des erreurs
 */
function formulaires_evaluation_verifier_dist($identifiant=0, $retour='', $objet='', $id_objet=0){
	$erreurs = array();
	#$erreurs = array('message_erreur'=>'héhé !');
	#var_dump($_POST);
	return $erreurs;
}

/**
 * Traitement du formulaire d'évaluation
 *
 * Traiter les champs postés
 *
 * @param int|string $évaluation
 *     Identifiant de l'évaluation
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $objet
 *     Type d'objet sur quoi porte l'évaluation
 * @param int $id_objet
 *     Identifiant d'objet sur quoi porte l'évaluation
 * @return array
 *     Retours des traitements
 */
function formulaires_evaluation_traiter_dist($identifiant=0, $retour='', $objet='', $id_objet=0){
	$res = array();

	include_spip('inc/session');
	$id_evaluation = evaluations_obtenir_identifiant($identifiant);
	$id_auteur = session_get('id_auteur');

	$criteres = _request('critere');

	$date = date('Y-m-d H:i:s', time());
	$reponses = array();
	$maj = false; // c'est une mise à jour ?

	// si l'auteur avait déjà répondu auparavant, c'est une mise à jour de ses réponses
	if ($critiques = sql_allfetsel('*', 'spip_evaluations_critiques', array(
		'id_evaluation = ' . sql_quote($id_evaluation),
		'objet = '         . sql_quote($objet),
		'id_objet = '      . sql_quote($id_objet),
		'id_auteur = '     . sql_quote($id_auteur),
	))) {
		foreach ($critiques as $c) {
			$critere = $c['id_evaluations_critere'];
			// ce critère a été reposté, on l'actualise
			if (isset($criteres[$critere])) {
				$maj = true; // c'est une mise à jour
				$vals = $criteres[$critere];
				$vals['date'] = $date;
				sql_updateq('spip_evaluations_critiques', $vals,
					'id_evaluations_critique=' . sql_quote($c['id_evaluations_critique']));

				unset($criteres[$critere]);
			}

		}
	}

	// pour toutes les réponses qui n'ont pas été actualisées au dessus,
	// on crée l'enregistrement en base
	foreach ($criteres as $id => $saisie) {
		$reponse = array();
		$reponse['id_evaluation'] = $id_evaluation;
		$reponse['id_evaluations_critere'] = intval($id);
		$reponse['objet'] = $objet;
		$reponse['id_objet'] = $id_objet;
		$reponse['id_auteur'] = $id_auteur;
		$reponse['date'] = $date;

		// avoir le même nombre de champ pour chaque réponse, pour faire 1 seule requête d'insertion.
		foreach (array('note'=>0, 'commentaire'=>'', 'forces'=>'', 'faiblesses'=>'') as $quoi => $defaut) {
			if (isset($saisie[$quoi])) {
				$reponse[$quoi] = $saisie[$quoi];
			} else {
				$reponse[$quoi] = $defaut;
			}
		}

		$reponses[] = $reponse;
	}

	if ($reponses) {
		sql_insertq_multi('spip_evaluations_critiques', $reponses);
	}

	if ($maj) {
		$res['message_ok'] = _T('evaluation:info_reponses_mises_a_jour');
	} else {
		$res['message_ok'] = _T('evaluation:Merci pour votre évaluation');
	}

	if ($retour) {
		$res['redirect'] = $retour;
	}

	return $res;
}

