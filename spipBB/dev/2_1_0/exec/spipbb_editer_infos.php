<?php
/*
| h. 10/11
*/
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions'); // pour fonction ajax_retour() 
include_spip('inc/spipbb_common');
spipbb_log('included',3,__FILE__);

include_spip('inc/spipbb_auteur_infos');

function exec_spipbb_editer_infos_dist() {
	$id_auteur=intval(_request('id_auteur'));
	$ret=spipbb_auteur_infos($id_auteur);
	ajax_retour($ret);
}
?>
