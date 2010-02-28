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
function _T_ou_typo($valeur, $args=array()) {
	
	// Si la valeur est bien une chaine (et pas non plus un entier déguisé)
	if (is_string($valeur) and !intval($valeur)){
		// Si la chaine est du type <:truc:> on passe à _T()
		if (preg_match('/^\<:(.*?):\>$/', $valeur, $match)) 
			$valeur = _T($match[1], $args);
		// Sinon on la passe a typo()
		else {
			include_spip('inc/texte');
			$valeur = typo($valeur);
		}
	}
	// Si c'est un tableau, on reapplique la fonction recursivement
	elseif (is_array($valeur)){
		foreach ($valeur as $cle => $valeur2){
			$valeur[$cle] = _T_ou_typo($valeur2, $args);
		}
	}

	return $valeur;

}
if (defined('_BONUX_STYLE'))
	_chemin(_DIR_PLUGIN_SPIP_BONUX."spip21/");

?>
