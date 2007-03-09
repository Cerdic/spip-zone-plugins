<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_add_row_dist()
{
	//$securiser_action = charger_fonction('securiser_action', 'inc');
	//$arg = $securiser_action();
	$arg = _request('arg');

	$arg = explode(':',$arg);
	$id_form = $arg[0];
	$id_donnee = $arg[2];
	$id_donnee_prev = $arg[1];
	$rang = 0;
	$niveau = "select_1_1";
	
	if ($id_donnee>0){
		# attraper le rang
		$res = spip_query('SELECT rang,id_form FROM spip_forms_donnees WHERE id_donnee='._q($id_donnee));
		if ($row = spip_fetch_array($res)) $rang = $row['rang'];
	}
	if ($id_donnee_prev>0)
		$niveau = Forms_les_valeurs($id_form,$id_donnee_prev,'select_1'," ",true);
	$new = 0;
	$c = array('ligne_1'=>_L("Nouvelle ligne"),"select_1"=>$niveau);
	list($new,$erreur) = Forms_creer_donnee($id_form,$c);
		
	if ($new && $rang)
		Forms_rang_update($new,$rang);

	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}
}

?>