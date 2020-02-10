<?php
/**
 * Plugin Agenda 4 pour Spip 3.2
 * Licence GPL 3
 *
 * 2006-2020
 * Auteurs : cf paquet.xml
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Affichage de la date d'un evenement en precisant le fuseau *si* config active et il y a une timezone sur l'evenement
 * @param string $date_debut
 * @param string $date_fin
 * @param string $horaire
 * @param string $timezone
 * @param string $forme
 * @return string
 */
function affdate_debut_fin_timezone($date_debut, $date_fin, $horaire='oui', $timezone='', $forme='') {
	static $config_timezone;
	if (is_null($config_timezone)) {
		include_spip('inc/config');
		$config_timezone = lire_config('agenda/fuseaux_horaires',0);
	}
	$tz_string = '';
	$h = ($horaire === 'oui' or $horaire === true);
	if ($h and $timezone and $config_timezone) {
		$date_debut = agenda_tz_date_local_to_tz($date_debut, $timezone);
		$date_fin = agenda_tz_date_local_to_tz($date_fin, $timezone);
		$tz_string = " <i class='tz'>(".agenda_tz_to_string($date_debut, $timezone, $forme).")</i>";
	}

	$aff = affdate_debut_fin($date_debut, $date_fin, $horaire, $forme) . $tz_string;
	return $aff;
}


/**
 * Formate l'affichage du nom la timezone
 * @param string $date
 * @param string $timezone
 * @param string $forme
 * @return string
 */
function agenda_tz_to_string($date, $timezone, $forme) {
	$timezone = agenda_tz_valide_timezone($timezone);

	try {
		$dtz = new DateTimeZone($timezone);
		$dt = new DateTime($date, $dtz);
	}
	catch (Exception $e) {
		return $timezone;
	}

	if (stripos($forme,'gmt')!==false) {
    return "GMT" . $dt->format('P');
	}
	if (stripos($forme,'tzshort')!==false) {
    return $dt->format('T');
	}

	return $dt->format('e');
}


/**
 * Recuperer la timezone PHP par defaut
 * @return false|string
 */
function agenda_tz_defaut() {
	return date('e');
}

/**
 * Selecteur de fuseau horaire SMART : propose en premier les 15 plus utilises
 *
 * @param string $timezone
 * @param string $name
 * @param string $id
 * @return string
 */
function agenda_tz_affiche_selecteur($timezone, $name, $id=null) {
	if (!$timezone) {
		$timezone = agenda_tz_defaut();
	}
	if (!$id) {
		$id=$name;
	}

	$prefered = sql_allfetsel("DISTINCT timezone_affiche", "spip_evenements");
	if (count($prefered) > 15) {
		$prefered = sql_allfetsel("timezone_affiche, count(id_evenement) AS N", "spip_evenements",'', "timezone_affiche", 'N DESC', '0,15');
	}
	$prefered = array_column($prefered, 'timezone_affiche');
	$prefered[] = agenda_tz_defaut();

	$out = "<select name=\"$name\" id=\"$id\">";
	$first = $all = "";
	foreach(timezone_identifiers_list() as $k => $tz) {
		$selected = (($tz === $timezone) ? ' selected="selected"' : '');
		$option = "<option val=\"$tz\"$selected>$tz</option>";
		if (in_array($tz, $prefered)) {
			$first .= $option;
			$option = str_replace($selected,"", $option);
		}

		$all .= $option;
	}
	$out .=
		"<optgroup label=\"". attribut_html(_T('agenda:evenement_timezone_most_used'))."\">$first</optgroup>\n"
		. "<optgroup label=\"". attribut_html(_T('agenda:evenement_timezone_all'))."\">$all</optgroup>\n";
	$out .= "</select>";
	return $out;
}

/**
 * Valider un nom de zone
 * @param string $timezone
 * @return string
 */
function agenda_tz_valide_timezone($timezone) {
	if ($timezone
	  and $dtz = new DateTimeZone($timezone)
	  and $dtz->getName()) {
		return $dtz->getName();
	}
	else {
		return '';
	}
}

/**
 * Convert a date from a TimeZone to another TimeZone
 * @param string $date
 * @param $origin_tz
 * @param $remote_tz
 * @return string
 */
function agenda_tz_date_tztotz($date, $origin_tz, $remote_tz) {
	try {
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime($date, $origin_dtz);
		$remote_dt = new DateTime($date, $remote_dtz);
	}
	catch (Exception $e) {
		return $date;
	}
	$offset = $remote_dtz->getOffset($remote_dt) - $origin_dtz->getOffset($origin_dt);
	$t = strtotime($date) + $offset;
	return date('Y-m-d H:i:s', $t);
}

/**
 * Convertir une date de la reference locale (timezone par defaut dans PHP) vers la timezone de l'evenement
 * @param string $date
 * @param string $timezone
 * @return string
 */
function agenda_tz_date_local_to_tz($date, $timezone='') {
	$timezone = agenda_tz_valide_timezone($timezone);
	$default_timezone = agenda_tz_valide_timezone(agenda_tz_defaut());
	if (!$timezone or !$default_timezone) {
		return $date;
	}
	return agenda_tz_date_tztotz($date, $default_timezone, $timezone);
}

/**
 * Convertir une date de la timezone de l'evenement vers la reference locale (timezone par defaut dans PHP)
 * @param $date
 * @param string $timezone
 * @return string
 */
function agenda_tz_date_tz_to_local($date, $timezone='') {
	$timezone = agenda_tz_valide_timezone($timezone);
	$default_timezone = agenda_tz_valide_timezone(agenda_tz_defaut());
	if (!$timezone or !$default_timezone) {
		return $date;
	}
	return agenda_tz_date_tztotz($date, $timezone, $default_timezone);
}
