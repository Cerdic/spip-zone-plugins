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
function messages_personnalises_fond_reservation_body_dist($args) {

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
	$champs_disponibles = array();
	$champs_sql = array();
	foreach ($tables AS $objet => $liste_champs) {
		foreach($liste_champs AS $champ) {
			if (!in_array($champ, $exclus)) {
				$alias = $objet . '_' . $champ;
				$champs_disponibles[] = $alias;
				$champs_sql[] = $objet . '.' . $champ . ' AS ' .$alias;
			}
		}
	}

	// les champs extras auteur
	include_spip('cextras_pipelines');

	$champs_lies = array(
		'auteur_nom' => 'reservation_nom',
		'auteur_email' => 'reservation_email'
	);
	if (function_exists('champs_extras_objet')) {
		$valeurs['champs_extras_auteurs'] = champs_extras_objet(table_objet_sql('auteur'));

		foreach ($valeurs['champs_extras_auteurs'] as $value) {
			$champ = $value['options']['nom'];
			$champs_lies['auteur_' . $champ] = 'reservation_' . $champ;
		}
	}

	return array(
		'label' => _T('reservation:titre_reservation'),
		'objet' => 'reservation',
		'fond' => 'notifications/contenu_reservation_mail',
		'declencheurs' => array(
			'statut' => array(
				'data' => $statuts,
			),
			'qui' => array(
				'data' =>
					array(
						'client' => _T('reservation:notifications_client_label'),
						'vendeur' => _T('reservation:notifications_vendeur_label'),
					),
			),
		),
		'raccoursis' => array(
			'requete' => array(
				'champs' => $champs_sql,
				'from' =>'spip_reservations AS reservation LEFT JOIN spip_auteurs AS auteur USING(id_auteur)'
			),
			'champs' => array(
				'disponibles' => $champs_disponibles,
				'lies' => $champs_lies,
			),
			'inclures' => array(
				'reservations' => array(
					'fond' => 'inclure/reservation',
					'titre' => _T('reservation:mp_titre_reservation_details'),
				),
			),
		),
	);
}
