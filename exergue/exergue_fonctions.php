<?php

function nettoyer_exergues($texte){
	$texte = preg_replace(',</?exergue ?/?>,Uims','',$texte);
	$texte = preg_replace(",<br class='exergue_ancre' ?/?>,Uims",'',$texte);
	$texte = preg_replace(",<br class=\"exergue_ancre\" ?/?>,Uims",'',$texte);

	return $texte ;
}
