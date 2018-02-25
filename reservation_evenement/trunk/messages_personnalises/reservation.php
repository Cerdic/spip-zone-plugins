<?php
/**
 * Définitions pour la personnalisation du message pour le plugin
 * Message personnalisé https://github.com/abelass/message_personnalise.
 *
 * @plugin     Réservation suivi
 * @copyright  2018
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_suivi\Mp_messages
 */

/**
 *
 * @param array $args
 *        	Variables du contexte.
 * @return array Définition.
 */
function messages_personnalises_reservation_dist($args) {

	// Les champs reservations
	$reservations = lister_tables_objets_sql('spip_reservations');
	$statuts = array();
	foreach ($reservations['statut_textes_instituer'] as $statut => $chaine) {
		$statuts[$statut] = _T($chaine);
	}

	// Les champs auteurs
	$auteurs = lister_tables_objets_sql('spip_auteurs');

	$tables = array(
		'reservation' => array_keys($reservations['field']),
		'auteur' => array_keys($auteurs['field']),
	);
	$exclus = array('pass',
		'low_sec',
		'htpass',
		'en_ligne',
		'alea_actuel',
		'alea_futur',
		'prefs',
		'cookie_oubli',
		'imessage',
		'messagerie',
		'source',
		'maj',
	);
	$champs = array();
	$champs_sql = array();
	foreach ($tables AS $objet => $liste_champs) {
		foreach($liste_champs AS $champ) {
			if (!in_array($champ, $exclus)) {
				$alias = $objet . '_' . $champ;
				$champs[] = $alias;
				$champs_sql[] = $objet . '.' . $champ . ' AS ' .$alias;
			}
		}
	}

	return array(
		'nom' => _T('reservation:titre_reservation'),
		'declencheurs' => array(
			'statut' => $statuts,
			'qui' => array(
				'client' => _T('reservation:notifications_client_label'),
				'vendeur' => _T('reservation:notifications_vendeur_label')
			),
		),
		'champs_disponibles' => $champs,
		'inclures' => array(
				'reservations' => array(
						'fond' => 'inclure/reservation',
						'titre' => _T('reservation:mp_titre_reservation_details'),
				),
		),
		'requete' => array(
			'champs' => $champs_sql,
			'from' =>'spip_reservations AS reservation LEFT JOIN spip_auteurs AS auteur USING(id_auteur)'
		),
		'fond' => 'notifications/contenu_reservation_mail',
	);
}
