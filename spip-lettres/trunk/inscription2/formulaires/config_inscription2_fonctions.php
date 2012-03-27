<?php
/**
 * 
 * Fonction de post-traitement du formulaire de configuration CFG
 * Crée les champs dans la table spip_auteurs_elargis dès la validation du CFG
 * 
 */
function cfg_config_inscription2_post_traiter(&$cfg){
	$verifier_tables = charger_fonction('inscription2_verifier_tables','inc');
	$verifier_tables();
}
?>
