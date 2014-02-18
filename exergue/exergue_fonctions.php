<?php

function nettoyer_exergues($texte){
	$texte = preg_replace(',</?exergue ?/?>,Uims','',$texte);
	return $texte ;
}
