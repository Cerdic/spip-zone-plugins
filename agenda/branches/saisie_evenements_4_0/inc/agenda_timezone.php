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
 * @param string $format
 *   peux prendre plusieurs formats separes par des espaces dans la chaine, dont
 *     ceux supportes par affdate_debut_fin
 *     + ceux supportes par agenda_tz_to_string
 * @return string
 */
function affdate_debut_fin_timezone($date_debut, $date_fin, $horaire='oui', $timezone='', $format='') {
	static $config_timezone;
	if (is_null($config_timezone)) {
		include_spip('inc/config');
		$config_timezone = lire_config('agenda/fuseaux_horaires',0);
	}
	$tz_string = '';
	$h = ($horaire === 'oui' or $horaire === true);
	if ($timezone and $config_timezone) {
		$date_debut = agenda_tz_date_local_to_tz($date_debut, $timezone);
		$date_fin = agenda_tz_date_local_to_tz($date_fin, $timezone);
		if ($h) {
			$tz_string = agenda_tz_to_string($date_debut, $timezone, $format);
			if ($tz_string) {
				$tz_string = " <i class='date-tz'>$tz_string</i>";
			}
		}
	}

	$aff = affdate_debut_fin($date_debut, $date_fin, $horaire, $format) . $tz_string;
	return $aff;
}


/**
 * Afficher la date dans la TimeZone indiquee selon le format choisi
 * @param $date
 * @param $timezone
 * @param $format
 *   peux prendre plusieurs formats separes par des espaces dans la chaine, dont
 *     ceux supportes par affdate
 *     + ceux supportes par agenda_tz_to_string
 *     + tzonly pour n'afficher que la timezone
 * @return string
 */
function affdate_timezone($date, $timezone, $format) {
	static $config_timezone;
	if (is_null($config_timezone)) {
		include_spip('inc/config');
		$config_timezone = lire_config('agenda/fuseaux_horaires',0);
	}
	$tz_string = '';
	if ($timezone and $config_timezone){
		$date = agenda_tz_date_local_to_tz($date, $timezone);
		$tz_string = agenda_tz_to_string($date, $timezone, $format);
	}
	if (stripos($format,'tzonly')!==false) {
		return $tz_string;
	}
	if ($tz_string) {
		$tz_string = " <i class='date-tz'>$tz_string</i>";
	}

	$aff = affdate($date, $format) . $tz_string;
	return $aff;
}


/**
 * Formate l'affichage du nom la timezone
 * @param string $date
 * @param string $timezone
 * @param string $format
 *   gmt : decalage horaire par rapport a GMT : GMT+02:00
 *   tzshort : format court (EDT, EST, GMT...)
 *   tznone : la zone n'est pas affichee
 *   tzfull (default) : format complet (Europe/Paris, America/New-York...)
 * @return string
 */
function agenda_tz_to_string($date, $timezone, $format) {
	$timezone = agenda_tz_valide_timezone($timezone);

	try {
		$dtz = new DateTimeZone($timezone);
		$dt = new DateTime($date, $dtz);
	}
	catch (Exception $e) {
		return $timezone;
	}

	if (stripos($format,'gmt')!==false) {
    return "GMT" . $dt->format('P');
	}
	if (stripos($format,'tzshort')!==false) {
    return $dt->format('T');
	}
	if (stripos($format,'tznone')!==false) {
    return '';
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
