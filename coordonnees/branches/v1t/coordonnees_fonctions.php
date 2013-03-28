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
function logo_type_adr($type_adresse) {
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
 * Filtre d'affichage du type (format) d'un courriel
 *
 * @param string $format_email
 *  Valeur du format d'adresse de courriel (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * IANA/RFC2426 (section 3.3.2) : internet (SMTP) pref x400 (X.400)
 *  Ces formats correspondent en fait a des "services" associes, et Internet est
 * pour le SMTP par defaut. Aussi, certaines applications ajoutent leur variante
 * proprietaire/personnelle : AOL (America On-Line), AppleLink (AppleLink), CIS
 * (CompuServe Information Service), eWorld (eWorld), IBMMail (IBM Mail), MCIMail
 * (MCI Mail), POWERSHARE (PowerShare), PRODIGY (Prodigy information service),
 * TLX (Telex number), TTMail (AT&T Mail), etc. Bien que non pris en charge
 * nativement, ils peuvent etre utilise en surchargeant le fichier listant les
 * types puis en rajoutant le logo (Compuserve-GIF/JPEG/PNG) dans le repertoire
 * "images/" et en donnant l'intitule dans sons fichier de langue perso "lang/perso_??.html"
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_email($format_email) {
	return logo_type_('email', $format_email);
}

/**
 * Filtre d'affichage du type (usage) d'un courriel
 *
 * @param string $type_email
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520+RFC5322/RFC6350 (section 6.4.2) : home (perso) intl work (pro)
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_mel($type_email) {
	return logo_type_('mel', $type_email);
}

/**
 * filtre d'affichage du type d'une messagerie de presence
 *
 * @param string $type_messagerie
 *  Valeur du type de liaison (cf. logo_type_).
 *  Les valeurs nativement prises en compte sont les codes normalisees
 * CCITT.X520+RFC5322/RFC6350 (section 6.4.3) : pref
 * @return string
 *  Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_impp($type_messagerie) {
	return logo_type_('impp', $type_messagerie);
}

/**
 * Interdire l'acces a une page si on n'a pas l'autorisation
 *
 * @param bool $autorisation
 *   Resultat de l'appel a autoriser('UneAction', 'coordonnees')
 * @return void
 *   Affichage de la page d'acces refuse/interdit si l'autorisation est a FALSE
 * @note
 *   C'est une reprise (r67625) du filtre "sinon_interdire_acces" de Bonux
**/
function coordonnees_interdit_sinon($autorisation) {
	if (!$autorisation) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
}

?>