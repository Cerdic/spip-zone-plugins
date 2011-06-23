<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
	include_spip('base/abstract_sql');

include_spip('inclure/affiche_banniere');

// Balise independante du contexte ici
function balise_BANNIERE ($p) {
	return calculer_balise_dynamique($p, 'BANNIERE', array());
}

// Balise de traitement des données
function balise_BANNIERE_dyn($position='1',$contexte='',$pays='') {
	return affiche_banniere($position='1',$contexte='',$pays='');
}

?>
