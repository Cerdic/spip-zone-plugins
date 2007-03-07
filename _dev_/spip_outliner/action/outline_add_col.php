<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/forms');
include_spip('base/forms_base_api');
function action_outline_add_col_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$arg = explode(':',$arg);
	$id_form = $arg[0];
	$champ = $arg[1];
	$champ_prev = $arg[2];
	$rang = 0;
	
	if ($champ){
		# attraper le rang
		$res = spip_query('SELECT rang FROM spip_forms_champs WHERE id_form='._q($id_form).' AND champ='._q($champ));
		if ($row = spip_fetch_array($res)) $rang = $row['rang'];
	}
	include_spip('inc/forms_edit');
	$new = Forms_insere_nouveau_champ($id_form,'texte',_L('Nouvelle Colonne'));
	#on le met public
	spip_query("UPDATE spip_forms_champs SET public='oui' WHERE id_form="._q($id_form)." AND champ="._q($new));
	
	#on lui fixe son rang si besoin
	if ($rang){
		spip_query("UPDATE spip_forms_champs SET rang=rang+1 WHERE id_form="._q($id_form)." AND rang>="._q($rang));
		spip_query("UPDATE spip_forms_champs SET rang="._q($rang)." WHERE id_form="._q($id_form)." AND champ="._q($new));
	}
	if ($redirect = urldecode(_request('redirect'))){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}	
}

?>