<?php
include_once("boucleLangues.php");
include_once("boucleFichiers.php");
include_once("boucleFor.php");
include_once("boucleIf.php");
include_once("boucleTableau.php");

/**
 * retourne la valeur d'un meta
 * utile pour faire des conditions sur la config courante
 */
function balise_META($p) {
	if ($p->param && !$p->param[0][0]) {
		$meta =  calculer_liste($p->param[0][1],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
		array_shift($p->param);
	}
	$p->code = 'lire_meta('.$meta.')';
	$p->statut = 'php';
	return $p;
}

?>
