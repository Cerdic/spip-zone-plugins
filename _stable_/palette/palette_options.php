<?php
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
  $config_palette = array(
  'palette_public' => 'on', // la valeur 'on' active Palette pour le site public
  'palette_ecrire' => 'on'  // la valeur 'on' active Palette pour l'espace privé 
  );
  ecrire_meta('palette', serialize(array('palette_public' => 'on', 'palette_ecrire' => 'on')));
  if (version_compare($GLOBALS['spip_version_code'],'1.9300','<')) ecrire_metas();
}
?>