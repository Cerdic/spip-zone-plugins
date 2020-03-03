<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'configs_description' => 'Ce plugin ajoute une table de configuration générale : spip_configs
	et un balise #MA_CONFIG\{ma_valeur\} ou #MA_CONFIG\{prefixe/nom_valeur\}.
	Pour permettre l\'édition des configs il faut activer le plugin crayons 
	et dans sa configuration cocher "Crayons dans le privé" et mettre "configurer_configs" dans le champ "Pages autorisées"',
	'configs_nom' => 'SPIP Configs ',
	'configs_slogan' => 'Regrouper les configs des plugins dans une meme table',
);
