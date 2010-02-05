<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

 /**
 * une fonction qui regarde si $texte est une chaine de langue
 * de la forme <:qqch:>
 * si oui applique _T()
 * si non applique typo()
 */
function _T_ou_typo($texte, $args=array()) {
	
	if (preg_match('/^\<:(.*?):\>$/',$texte,$match)) {
		static $traduire=false ;
		if (!$traduire) {
			$traduire = charger_fonction('traduire', 'inc');
			include_spip('inc/lang');
		}
		$text = $traduire($match[1],$GLOBALS['spip_lang']);
	}
	
	else {
		include_spip('inc/texte');
		$text = typo($texte);
	}

	if (is_array($args))
	foreach ($args as $name => $value)
		$text = str_replace ("@$name@", $value, $text);

	return $text;

}

?>
