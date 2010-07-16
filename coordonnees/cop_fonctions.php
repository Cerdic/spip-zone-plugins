<?php
/**
 * Plugin Coordonnees 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

function logo_type_adresse($type_adresse) {
	static $types = array(
		'pro' 		=> array('images/type_pro-16.png', 		'cop:adresse_pro'),
		'perso' 	=> array('images/type_domicile-16.png', 	'cop:adresse_perso'),
		'fax' 		=> array('images/type_fax-16.png', 		'cop:fax'),
		#'mobile' 	=> array('images/type_mobile-128.png', 		'cop:mobile'),
	);
	$type = substr(strtolower($type_adresse),0,5);
	if (isset($types[$type])) {
		$im = $types[$type];
		return '<img src="' . find_in_path($im[0]) . '" alt="' . _T($im[1]) . '" title="' . _T($im[1]) . '" />';
	}
	
	return '';
}

?>
