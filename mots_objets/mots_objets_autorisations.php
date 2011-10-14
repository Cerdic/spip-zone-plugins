<?php
/**
 * Plugin mots-objets pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 * Grâce au soutien actif de Matthieu Marcillaud - Magraine
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
// fonction du pipeline, n'a rien a faire.
function mots_objets_autoriser() {}


// renvoyer chaque objet sur la fonction générique dans l'autorisation de rubrique.
include_spip('inc/gouverneur_de_mots');
foreach (gouverneur_de_mots() as $objet) {
	$obj = $objet->objet;
	$function = "
		function autoriser_${obj}_editermots_dist(\$faire,\$quoi,\$id,\$qui,\$opts) {
			return autoriser_rubrique_editermots_dist(\$faire, '$obj', 0, \$qui, \$opts);
		}";
	eval($function);
}


?>
