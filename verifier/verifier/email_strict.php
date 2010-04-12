<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
 * OBSOLETE : remplacé par $verifier($valeur, 'email',array('mode'=>'strict'))
 * Assurer la compatibilité un temps
 */
function verifier_email_strict_dist($valeur, $options=array()){
	$verifier = charger_fonction('verifier','inc');
	if(!$verifier($valeur, 'email',array('mode'=>'strict'))) return false;
	else return _T('verifier:erreur_email');
}
