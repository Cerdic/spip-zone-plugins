<?php

function balise_DEBUT_TEXTE_HEAD($p) {
	$p->code = "'<!-- spip_debut_texte_head".$GLOBALS["cle_head"]."-->'";
	return $p;
}

function balise_FIN_TEXTE_HEAD($p) {
	$p->code = "'<!-- spip_fin_texte_head".$GLOBALS["cle_head"]."-->'";
	return $p;
}

?>
