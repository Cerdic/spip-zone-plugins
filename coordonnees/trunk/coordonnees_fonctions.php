<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Fonction privee mutualisee utilisee par les filtres logo_type_xx
 * Renvoit soit une balise <img> si elle est trouvee, soit une balise <abbr>
 * 
 * @param string $id    adr, tel, email, mel
 * @param string $val   le type de coordonnee (dom, home, work etc.)
 * @return string       balise <img> ou <abbr>   
**/ 
function logo_type_($id='', $val='') {
	include_spip('inc/utils');
	global $formats_logos;
	$type = strtolower($val);
	$lang = _T( ($id ? ('coordonnees:type_'. $id) : 'perso:type' )  . '_'.$type ); // les types libres sont traites par le fichier de langue perso
	foreach ($formats_logos as $format) { // inspiration source: ecrire/inc/chercher_logo.php
		$fichier = 'type'. ($id ? ('_' . $id) : '') . ($type ? ('_' . $type) : '') . '.' . $format;
		if ( $chemin = chemin_image($fichier) )
			$im = $chemin;
	}
	if ($im)
		return '<img class="type" src="' . $im . '" alt="' . $type . '" title="' . $lang . '" />';
	elseif ($type)
		return '<abbr class="type" title="' . $type . '">' . $lang . '</abbr>';
	else
		return '';
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'une adresse
 *
 * @param string $type_adresse    RFC2426/CCITT.X520 : dom home intl parcel postal pref work
 * @return string                 balise <img> ou <abbr>
**/ 
function filtre_logo_type_adr($type_adresse) {
	return logo_type_('adr', $type_adresse);
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'un numero de tel
 *
 * @param string $type_tel    RFC2426/CCITT.X500 : bbs car cell fax home isdn modem msg pager pcs pref video voice work
 *                            RFC6350/CCITT.X520.1988 : cell fax pager text textphone video voice x-... (iana-token)
 *                            + : dsl
 * @return string             balise <img> ou <abbr>
**/ 
function filtre_logo_type_tel($type_numero) {
	return logo_type_('tel', $type_numero);
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'un email
 *
 * @param string $type_adresse    RFC2426/IANA : internet pref x400
 *                                RFC6350/CCITT.X520+RFC5322 : home intl work
 * @return string                 balise <img> ou <abbr>
**/ 
function filtre_logo_type_email($type_email) {
	return logo_type_('email', $type_email);
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'un mel (email)
 *
 * @param string $type_adresse    RFC6350/CCITT.X520+RFC5322 readapte : perso pro
 * @return string                 balise <img> ou <abbr>
**/ 
function filtre_logo_type_mel($type_email) {
	return logo_type_('mel', $type_email);
}

?>
