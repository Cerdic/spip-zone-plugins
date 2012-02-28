<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/precharger_objet');
function inc_precharger_album_dist($id_album, $lier_trad=0) {
	return precharger_objet('album', $id_album, $lier_trad, 'nom');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_album_dist($id_album, $lier_trad=0) {
	return precharger_traduction_objet('album', $id_album, $lier_trad, 'nom');
}

?>
