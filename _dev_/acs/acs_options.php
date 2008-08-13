<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

// Dossier des paramètres et images utilisateur
// User images and parameters
$GLOBALS['ACS_CHEMIN'] = 'IMG/_acs';

// Pages de l'espace ecrire à accès contrôlé comme ACS - ACS access rules controlled pages
// Les administrateurs sont ceux d'ACS
$GLOBALS['ACS_ACCES'] = array('mots_type');

// Contrôle d'accès ACS aux pages d'administration de certains plugins, s'ils sont installés
if(in_array('notation', $GLOBALS['plugins'])) $GLOBALS['ACS_ACCES'][] = 'notation_param';
if(in_array('w3c_go_home', $GLOBALS['plugins'])) $GLOBALS['ACS_ACCES'][] = 'w3c_go_home';
if(in_array('openPublishing', $GLOBALS['plugins'])) {
  $GLOBALS['ACS_ACCES'][] = 'op';
  $GLOBALS['ACS_ACCES'][] = 'op_effacer';
}

//define('_DEBUG_CRAYONS', true);
/*__________________________________________________________________

  Ne PAS modifier ce qui suit - Do NOT modify anything after this
  __________________________________________________________________
*/
require_once _DIR_PLUGIN_ACS.'inc/acs_onload.php';

?>