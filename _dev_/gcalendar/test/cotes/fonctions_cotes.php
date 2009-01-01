<?php

function filtre0($texte) {
	$texte=ereg_replace("0$","",$texte);
	$texte=ereg_replace(".0$","",$texte);
	$texte=ereg_replace(".00$","",$texte);
	return $texte;
}

?>
