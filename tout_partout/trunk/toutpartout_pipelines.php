<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function toutpartout_pre_boucle($boucle){
	// À rendre configurable !
	if (in_array($boucle->type_requete, array('rubriques', 'auteurs')) AND !isset($boucle->modificateur['criteres']['statut'])){
		$boucle->modificateur['criteres']['statut'] = true;
	}
	
    return $boucle;
}
