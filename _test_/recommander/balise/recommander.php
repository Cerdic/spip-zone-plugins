<?php
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation
global $balise_RECOMMANDER_collecte;
$balise_RECOMMANDER_collecte = array();

function balise_RECOMMANDER ($p) {
	return calculer_balise_dynamique($p,'RECOMMANDER', array());
}

function balise_RECOMMANDER_stat($args, $filtres) {
	return $args;
}

function balise_RECOMMANDER_dyn($titre,$url='',$texte='',$subject='') {
	if (!$f = charger_fonction('fragment_recommander', 'action', true))
		die('erreur fragment_recommander absent');

	return array('recommander/noisette', 0, 
		array(
			'fragment'=>$f(true,array('titre'=>$titre,'url'=>$url,'texte'=>$texte,'subject'=>$subject)),
		));
}

?>
