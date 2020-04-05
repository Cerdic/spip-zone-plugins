<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ce wrapper permet d'être dans le bon répertoire pour appeller filepicker.php
 * et d'accéder ainsi aux variables globales et à l'API SPIP
 */

function action_filepickerwrapper() {
	include(_DIR_PLUGIN_ACS.'inc/picker/filepicker.php');
}
?>