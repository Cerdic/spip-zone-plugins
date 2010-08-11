<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

/**
 * Inserer le head de petronille avant la premiere css du head
 * @param string $head
 * @return string
 */
function petronille_pied_de_biche($head){
	$p = stripos($head, "<link");
	$h = recuperer_fond('inclure/petronille-head',array());

	$head = substr_replace($head, $h, $p, 0);
	return $head;
}

/**
 * Inserer le pied de biche dans le head
 *
 * @param string $flux
 * @return string
 */
function petronille_insert_head($flux){
	$flux .= "<"."?php header(\"X-Spip-Filtre: petronille_pied_de_biche\"); ?".">";
	return $flux;
}
?>