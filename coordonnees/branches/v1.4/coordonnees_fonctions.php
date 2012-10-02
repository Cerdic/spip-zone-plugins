<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

function logo_type_($id, $val, $taille=16) {
	global $formats_logos;
	$type = strtolower($val);
	foreach ($formats_logos as $format) { // @file ecrire/inc/chercher_logo.php
		$fichier = 'images/type_'. $id . '_' . $type . ($taille?"-$taille":'') '.' . $format;
		if ( find_in_path($fichier) )
			$im = $fichier . ($taille?('" width="'.$taille.'" height="'.$taille):'');
	}
	if ($type && $im)
		return '<img class="type" src="' . $im . '" alt="' . $type . '" title="' . _T('coordonnees:type_'. $id . '_'.$type) . '" />';
	elseif ($type)
		return '<abbr class="type" title="' . $type . '">' . _T('coordonnees:type_'. $id . '_'.$type) . '</abbr>';
	else
		return '';
}

function logo_type_adresse($type_adresse, $taille=16) {
	return logo_type_('adr', $type_adresse, $taille);
}

function logo_type_numero($type_numero, $taille=16) {
	return logo_type_('tel', $type_numero, $taille);
}

function logo_type_email($type_email, $taille=16) {
	return logo_type_('mel', $type_email, $taille);
}

?>