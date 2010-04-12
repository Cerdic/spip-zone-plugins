<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifier que le courriel utilisé n'est pas
 * déjà présent en base SPIP_AUTEURS
 */
function verifier_email_disponible_dist($valeur, $options=array()){
	$verifier = charger_fonction('verifier','inc');
	if(!$verifier($valeur, 'email',array('disponible'=>'oui'))) return false;
	else return _T('verifier:erreur_email_nondispo');
}
