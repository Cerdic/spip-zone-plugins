<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//include_spip('action/editer_objets');
function action_editer_bonplans_dist() {
	$editer_objets=charger_fonction('editer_objets','action');
	return $editer_objets();
}
?>