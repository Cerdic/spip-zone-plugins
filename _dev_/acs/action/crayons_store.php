<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/*
 * Ajoute des sources de données non-tables pour les crayons
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

// Inclusion de l'action crayons_store du plugin crayons
require_once(_DIR_PLUGIN_CRAYONS.'action/crayons_store.php');

// Définition de fonction de pinceaux (extensions des crayons)

// Fonction appellée par action/crayons_store.php
// Permet de gérer les crayons non liés aux tables
function source_valeur_colonne_table_dist($type, $name, $id) {
  
  // Source
  $name = implode($name);
  $page = str_replace('_slash_', '/', str_replace('_tiret_', '-', $name));
  $file = find_in_path($page.'.html');
  $source = file_get_contents($file);
  return array($name => $source);
}

function source_revision($id, $vals, $type, $wids) {
  // Dernière sécurité :Accès réservé aux admins ACS
  // Last security: access restricted to ACS admins 
  if (!in_array($GLOBALS['auteur_session']['id_auteur'], explode(',', $GLOBALS['meta']['ACS_ADMINS'])))
    return;

  $name = implode(array_keys($vals));
  $newsource = implode($vals);
  $page = str_replace('_slash_', '/', str_replace('_tiret_', '-', $name));  
  $file = find_in_path($page.'.html');
  if (@file_put_contents($file, $newsource) == false)
    $r = 'FAILED';
  else
    $r = 'done';
  spip_log("source_revision $r for $page by ".$GLOBALS['auteur_session']['id_auteur']." ($file)");
}

?>