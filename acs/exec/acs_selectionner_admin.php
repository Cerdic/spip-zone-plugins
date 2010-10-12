<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

# afficher un mini-navigateur des admins

// http://doc.spip.org/@exec_selectionner_auteur_dist
function exec_acs_selectionner_admin_dist() {
  $admid = _request('admid');
	$selectionner_admin = charger_fonction('acs_selectionner_admin', 'inc');
	ajax_retour($selectionner_admin($admid));
}
?>
