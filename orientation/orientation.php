<?php

/* Le plugin orientation ajoute le critere {portrait} , {carre} ou {paysage} pour le classement des photos */

// {portrait}
function critere_portrait_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = $crit->not
		?"'($table.largeur>0 AND $table.hauteur <= $table.largeur)'"
		:"'($table.largeur>0 AND $table.hauteur > $table.largeur)'";
}

// {paysage}
function critere_paysage_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = $crit->not
		?"'($table.largeur>0 AND $table.largeur <= $table.hauteur)'"
		:"'($table.largeur>0 AND $table.largeur > $table.hauteur)'";
}

// {carre}
function critere_carre_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = $crit->not?
		 "'($table.largeur != $table.hauteur)'"
		:"'($table.largeur>0 AND $table.largeur = $table.hauteur)'";
}

?>