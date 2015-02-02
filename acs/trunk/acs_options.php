<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * \file acs_options.php
 * \~english
 * First file of the plugin loaded.
 * Debug options and plugin load.
 * \~french
 * Premier fichier du plugin chargé.
 * Options de debug et chargement du plugin.
 */

// Uncomment for debug crayons :
//define('_DEBUG_CRAYONS', true);

define ('_DEBUG_AUTORISER', true);         // debug autorisations SPIP
define('_ACS_LOG', _LOG_DEBUG);            // niveau de log ACS
define('_LOG_FILTRE_GRAVITE', _LOG_DEBUG); // niveau de log SPIP
// Constantes de niveau de log de SPIP
//    0 (_LOG_HS)
//    1 (_LOG_ALERTE_ROUGE)
//    2 (_LOG_CRITIQUE)
//    3 (_LOG_ERREUR)
//    4 (_LOG_AVERTISSEMENT)
//    5 (_LOG_INFO_IMPORTANTE)
//    6 (_LOG_INFO)
//    7 (_LOG_DEBUG)

/*__________________________________________________________________

  Ne PAS modifier ce qui suit - Do NOT modify anything after this
  __________________________________________________________________
*/
// Chargement - Loading
require_once _DIR_PLUGIN_ACS.'inc/acs_onload.php';
?>