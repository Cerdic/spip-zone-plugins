<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_LICENCE_dist ($p){
	if (($logo = interprete_argument_balise (1, $p))==NULL)
		$logo = 'oui';
	if (($lien = interprete_argument_balise (2, $p))==NULL)
		$lien = 'oui';
	$p->code = "licence_affiche(".champ_sql('id_licence', $p).",".$logo.",".$lien.")";
	$p->interdire_scripts = false;
	return $p;
}

?>