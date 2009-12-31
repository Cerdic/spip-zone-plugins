<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

function action_forms_champs_deplace_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();
	
	$args = explode("-",$args);
	$id_form = intval($args[0]);
	$champ = $args[1];
	$action = $args[2];
	include_spip('inc/autoriser');
	if (autoriser('modifier','form',$id_form)
		// Monter / descendre un champ
	  AND in_array($action,array('monter','descendre'))){
		if ($row = sql_fetsel("rang","spip_forms_champs","id_form=".intval($id_form)." AND champ=".sql_quote($champ))) {
			$rang1 = intval($row['rang']);
			if ($action == 'monter')
				$row = sql_fetsel("rang","spip_forms_champs","id_form=".intval($id_form)." AND rang<$rang1","","rang DESC","0,1");
			else
				$row = sql_fetsel("rang","spip_forms_champs","id_form=".intval($id_form)." AND rang>$rang1","","rang","0,1");
			if ($row){
				$rang2 = intval($row['rang']);
				sql_update("spip_forms_champs",array("rang"=>"$rang1+$rang2-rang"),"id_form=".intval($id_form)." AND". sql_in("rang",array($rang1,$rang2)));
			}
		}
	}
}

?>