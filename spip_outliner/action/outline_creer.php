<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_creer_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('base/forms_api');
	$f = find_in_path('base/Outliner.xml');
	Forms_creer_table($f,'outline',false);

	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}	
}

?>