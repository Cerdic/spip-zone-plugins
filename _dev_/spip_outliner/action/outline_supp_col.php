<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_supp_col_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$arg = explode(':',$arg);
	$id_form = $arg[0];
	$champ = $arg[1];
	$champ_prev = $arg[2];
	$rang = 0;
	
	if ($champ)
		$res = spip_query("UPDATE spip_forms_champs SET public='non' WHERE id_form="._q($id_form).' AND champ='._q($champ));

	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}	
}

?>