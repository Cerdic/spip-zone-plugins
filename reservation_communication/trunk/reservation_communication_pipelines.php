<?php
/**
 * Utilisations de pipelines par Réservation Comunications
 *
 * @plugin     Réservation Comunications
 * @copyright  2015
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_communication\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;


/**
 * Ajouter les objets sur les vues de rubriques
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function reservation_communication_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec']) AND $e['type'] == 'rubrique' AND $e['edition'] == false) {

		$id_rubrique = $flux['args']['id_rubrique'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creerreservation_communicationdans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("reservation_communication:icone_creer_reservation_communication"), generer_url_ecrire("reservation_communication_edit", "id_rubrique=$id_rubrique"), "reservation_communication-24.png", "new", "right") . "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('reservation_communications', array(
			'titre' => _T('reservation_communication:titre_reservation_communications_rubrique'),
			'id_rubrique' => $id_rubrique,
			'par' => 'titre'
		));
		$flux['data'] .= $bouton;

	}
	return $flux;
}

/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function reservation_communication_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/reservation_communications', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('reservation_communication:info_reservation_communications_auteur')
		), array('ajax' => true));

	}
	return $flux;
}

/**
 * Ajoute un action dans le compteur rérvations ans l'espace admin (navigation)
 *
 * @pipeline reservation_compteur_action
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function reservation_communication_reservation_compteur_action($flux) {

	$flux['data'] = recuperer_fond('inclure/reservation_compteur_action', $flux);

	return $flux;
}

/**
 * Ajoute du contenu à la fiche de l'objet
 *
 * @pipeline afficher_complement_objet
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function reservation_communication_afficher_complement_objet($flux){
	if ($flux['args']['type']=='reservation_communication'
		AND $id_reservation_communication=intval($flux['args']['id'])){
		#ajouter la liste des envois
		$contexte = array('id_reservation_communication'=>$id_reservation_communication);
		if (_request('recherche'))
			$contexte['recherche'] = _request('recherche');
		$flux['data'] .= recuperer_fond("prive/squelettes/contenu/inc-reservation_communication-destinataires",$contexte,array('ajax'=>true));
	}
	return $flux;
}

/**
 * Définitions des notifications pour https://github.com/abelass/notifications_archive
 *
 * @pipeline notifications_archive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function reservation_communication_notifications_archive($flux) {
	$flux = array_merge($flux, array(
		'reservation_communication' => array(
			'activer' => 'on',
			'duree' => '180'
		),
	));

	return $flux;
}

/**
 * Ajouter une entré au menu de navigation de résrvation événement.
 *
 * @pipeline reservation_evenement_objets_navigation
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function reservation_communication_reservation_evenement_objets_navigation($flux) {

	$flux['data']['reservation_communications'] = array(
			'label' => _T('reservation_communication:titre_reservation_communications'),
			'objets' => array('reservation_communications', 'reservation_communications')
	);

	return $flux;
}
