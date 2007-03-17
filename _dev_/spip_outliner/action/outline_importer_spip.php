<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_importer_spip_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$outline_importer_spip = charger_fonction('outline_importer_spip','inc');
	$id_table = $outline_importer_spip();

	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}	
}

?>