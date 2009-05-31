<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_supprimer_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_form = intval(_request('id_form'));
	if ($id_form)
		Forms_supprimer_tables($id_form);

	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}	
}

?>