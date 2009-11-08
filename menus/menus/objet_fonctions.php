<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */


function exposer($id_objet, $objet, $env, $on='on', $off=''){
	$primary = id_table_objet($objet);
	include_spip('public/quete');
	return calcul_exposer($id_objet, $primary, unserialize($env), '', $primary) ? $on : $off;
}
?>