<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
if (!defined('_EVENEMENT_ICAL_X_PROPERTIES')) {
	define('_EVENEMENT_ICAL_X_PROPERTIES', false);
}
/**
 * La RFC 5545 qui décrit le format ICAL indique qu'on peut indiquer des propriété arbitraire en préfixant par X
 * La présente fonction ajoute ces champs arbitraire:
 *  * Addresse
 *  * Nombre de places et nombre de place restantes
 *  * Champs extra
 * Attention, ne s'appliquer que si _EVENEMENT_ICAL_X_PROPERTIES veut true
 * @param int $id_evenement
 * @return string
 */
function evenement_ical_X_properties($id_evenement) {
	if (!_EVENEMENT_ICAL_X_PROPERTIES) {
		return '';
	}
	$values = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement");

	// D'abord, le champ adresse
	$return = '';
	if ($values['adresse']) {
		$return = 'X-ADRESSE:'.filtrer_ical($values['adresse'])."\n";
	}

	// Puis les infos sur le nombre de places
	if ($values['inscription']) {
		$return .= 'X-INSCRIPTION:'.$values['inscription']."\n";
	}
	if ($values['places']) {
		$return .= 'X-PLACES:'.$values['places']."\n";
		$places_reservees = sql_countsel('spip_evenements_participants', array("id_evenement=$id_evenement",'reponse='.sql_quote('oui')));
		$places_restantes = $values['places'] - $places_reservees;
		$return .= 'X-PLACES-RESERVEES:'.$places_reservees."\n";
		$return .= 'X-PLACES-RESTANTES:'.$places_restantes."\n";
	}

	// Puis les champ extra qu'on peut voir
	if (test_plugin_actif('cextras')) {
		include_spip('cextras_pipelines');
		$cextras = champs_extras_objet('spip_evenements');
		$cextras = champs_extras_autorisation('voir', $objet, $cextras, array('id_objet' => $id_evenement));
		$cextras = array_keys($cextras);

		foreach ($cextras as $c) {
			if ($values[$c]) {
				$return .= 'X-'.strtoupper($c).':'.filtrer_ical($values[$c])."\n";
			}
		}
	}
	return $return;
}
