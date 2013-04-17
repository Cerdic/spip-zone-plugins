<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fichier de configuration du plugin Palette
 *
 * Si le plugin cfg est installé, les options définies dans cfg remplacent celles définies ici.
 *
 * Si cfg n'est pas installé, vous pouvez configurer ci dessous les deux options d'activation du plugin Palette
 * pour la partie publique et pour l'espace ecrire
 *
 */
if (!function_exists('lire_config')) {
	$options_palette = array(
		'palette_public' => '', // la valeur 'on' active Palette pour le site public
		'palette_ecrire' => 'on'  // la valeur 'on' active Palette pour l'espace privÃ©
	);
  	$GLOBALS['palette'] = serialize($options_palette);
}

define('_DIR_LIB_PALETTE','lib/farbtastic_1_3_2/')
?>
