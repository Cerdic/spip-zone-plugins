<?php
/**
 * D'apres le Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 *
 */

/**
 * Balise #COMPTEUR associee au critere compteur
 *
 * @param unknown_type $p
 * @return unknown
 */
function balise_COMPTEUR_dist($p) {
	$p->code = '';
	if (isset($p->param[0][1][0])
	AND $champ = ($p->param[0][1][0]->texte))
		return rindex_pile($p, "compteur_$champ", 'compteur');
  return $p;
}

?>