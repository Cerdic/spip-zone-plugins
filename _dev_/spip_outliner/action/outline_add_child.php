<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_add_child_dist()
{
	//$securiser_action = charger_fonction('securiser_action', 'inc');
	//$arg = $securiser_action();
	$arg = _request('arg');

	$arg = explode(':',$arg);
	$id_form = $arg[0];
	$id_donnee = $arg[2];
	$id_donnee_prev = $arg[1];
	
	$new = 0;
	$c = array('ligne_1'=>_L("Nouvelle ligne"));
	list($new,$erreur) = Forms_arbre_inserer_donnee($id_form,$id_donnee_prev,'fils_cadet',$c);
		
	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}
}

?>