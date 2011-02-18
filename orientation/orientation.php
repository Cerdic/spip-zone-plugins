<?php

/* Le plugin orientation ajoute le critere {portrait} , {carre} ou {paysage} pour le classement des photos */

// {portrait}
function critere_portrait($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = $crit->not
		?"'$table.hauteur <= $table.largeur'"
		:"'$table.hauteur > $table.largeur'";
}

// {paysage}
function critere_paysage($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = $crit->not
		?"'$table.largeur <= $table.hauteur'"
		:"'$table.largeur > $table.hauteur'";
}

// {carre}
function critere_carre($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$boucle->where[] = $crit->not
	?"'$table.largeur != $table.hauteur'"
	:"'$table.largeur = $table.hauteur'";
}

?>