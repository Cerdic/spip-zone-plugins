<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt
/**
 * Ce wrapper permet d'être dans le bon répertoire
 * et d'accéder aux variables globales
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_wrap() {
	if (isset($_GET['cadre'])) include($_GET['cadre']);
}
?>