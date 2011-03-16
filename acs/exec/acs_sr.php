<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_acs_sr() {
	acs_log('exec_acs_sr', 'acs');
	$acs_sr = charger_fonction('acs_sr', 'inc');
	ajax_retour($acs_sr());
}
?>