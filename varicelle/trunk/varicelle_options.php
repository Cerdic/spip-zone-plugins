<?php

if (!isset($GLOBALS['spip_pipeline']['affichage_final'])) {
	$GLOBALS['spip_pipeline']['affichage_final'] = '';
}
$GLOBALS['spip_pipeline']['affichage_final'] .= '|varicelle';

// Utiliser la class .btn dans SPIP :
function varicelle($texte) {
	$texte = str_replace('class="submit', 'class="btn submit', $texte);
	return $texte;
}
