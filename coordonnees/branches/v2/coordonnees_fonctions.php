<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
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

// RFC2426/CCITT.X520 : dom home intl parcel postal pref work
function logo_type_adr($type_adresse) {
	return logo_type_('adr', $type_adresse);
}

// RFC2426/CCITT.X500 : bbs car cell fax home isdn modem msg pager pcs pref video voice work
// RFC6350/CCITT.X520.1988 : cell fax pager text textphone video voice x-... (iana-token)
// + : dsl
function logo_type_tel($type_numero) {
	return logo_type_('tel', $type_numero);
}

// RFC2426/IANA : internet pref x400
// RFC6350/CCITT.X520+RFC5322 : home intl work
function logo_type_email($type_email) {
	return logo_type_('email', $type_email);
}

// RFC6350/CCITT.X520+RFC5322 readapte : perso pro
function logo_type_mel($type_email) {
	return logo_type_('mel', $type_email);
}

?>