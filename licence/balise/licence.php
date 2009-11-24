<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

function balise_LICENCE_dist ($p)
{
	$p->code = "licence_affiche(".champ_sql('id_licence', $p).")";
	$p->interdire_scripts = false;
	return $p;
}

?>