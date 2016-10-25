<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_gabarit_previsu_dist(){

	$contexte = $_POST;

	// mais il faut avoir le droit de previsualiser
	// (par defaut le droit d'aller dans ecrire/)
	if (!autoriser('previsualiser','gabarit'))
		$contexte = array();

	echo recuperer_fond('prive/gabarit_previsu',$contexte);
}

?>