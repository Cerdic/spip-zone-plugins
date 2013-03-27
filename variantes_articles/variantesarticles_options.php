<?php
/**
 * Plugin Variantes Articles
 * (c) 2013 Beurt
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


// On s'insère avant les variantes par rubrique et par langue...
$GLOBALS['spip_pipeline']['styliser'] = "|styliser_par_article".$GLOBALS['spip_pipeline']['styliser'];


// Inspiré de styliser_par_rubrique:
// si le squelette par défaut existe
// alors on vérifie si une variante existe
// pour un id_article donné sous la forme
// fond_XX.html
// (le séparateur est donc l'underscore
// pour se différencier des variantes par rubrique
// et par langue)
function styliser_par_article($flux) {

	// uniquement si un squelette a ete trouve
	if ($squelette = $flux['data']) {
		$ext = $flux['args']['ext'];
		if ($id_article = $flux['args']['id_article']) {
			$f = "$squelette"."_".$id_article;
			if (@file_exists("$f.$ext"))
				$squelette = $f;
			// sauver le squelette
			$flux['data'] = $squelette;
		}		
	}
	
	return $flux;

}

?>