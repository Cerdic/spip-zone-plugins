<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction privee mutualisee utilisee par les filtres logo_type_xx
 *
 * @param string $id
 *  Suffixe du xx du filtre logo_type_xx appelant ;
 *  Infixe du logo "images/type_xx_yy.???" a associer ;
 *  Correspond normalement a la classe vCard : adr, tel, email
 * @param string $val
 *  Valeur associee transmise par le filtre logo_type_xx ;
 *  Suffixe du logo "images/type_xx_yy.???" a associer ;
 *  Correspond au "type" de liaison de la la coordonnee (home, work, etc.)
 * @return string
 *  Balise <IMG> (s'il existe un logo "images/type_$id_$val") ou <ABBR> (sinon),
 * avec classes semantiques micro-format et traduction des valeurs cles RFC2426
 * @note
 *  http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html
 *  http://www.alsacreations.com/tuto/lire/1223-microformats-composes.html
 *
**/
function logo_type_($id='', $val='') {
	global $formats_logos;
	$type = strtolower($val);
	$lang = _T( ($id ? ('coordonnees:type_'. $id) : 'perso:type' )  . '_'.$type ); // les types libres sont traites par le fichier de langue perso
	foreach ($formats_logos as $format) { // inspiration source: ecrire/inc/chercher_logo.php
		$fichier = 'images/type'. ($id ? ('_' . $id) : '') . ($type ? ('_' . $type) : '') . '.' . $format;
		if ( $chemin = find_in_path($fichier) )
			$im = $chemin;
	}
	if ($im)
		return '<img class="type" src="' . $im . '" alt="' . $type . '" title="' . $lang . '" />';
	elseif ($type)
		return '<abbr class="type" title="' . $type . '">' . $lang . '</abbr>';
	else
		return '';
}

/**
 * Filtre d'affichage du type d'une adresse
 *
 * @param string $type_adresse
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520/RFC2426 (section 3.2.1) : dom home intl parcel postal pref work
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_adr($type_adresse) {
	return logo_type_('adr', $type_adresse);
}

/**
 * Filtre d'affichage du type d'un numero
 *
 * @param string $type_numero
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X500/RFC2426 (section 3.3.1) : bbs car cell fax home isdn modem msg pager pcs pref video voice work
 * CCITT.X520.1988/RFC6350 (section 6.4.1) : cell fax pager text textphone video voice x-... (iana-token)
 * ainsi que : dsl <http://fr.wikipedia.org/wiki/Digital_Subscriber_Line#Familles>
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_tel($type_numero) {
	return logo_type_('tel', $type_numero);
}

/**
 * Filtre d'affichage du type d'un courriel
 *
 * @param string $type_email
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * IANA/RFC2426 (section 3.3.2) : internet pref x400
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_email($type_email) {
	return logo_type_('email', $type_email);
}

/**
 * Filtre d'affichage du type (usage) d'un courriel
 *
 * @param string $type_email
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520+RFC5322/RFC6350 (section 6.4.2) : home (perso) intl work (pro)
**/
function filtre_logo_type_mel($type_email) {
	return logo_type_('mel', $type_email);
}

?>