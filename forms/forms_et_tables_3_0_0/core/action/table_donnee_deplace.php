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

function action_table_donnee_deplace_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	$args = explode("-",$args);
	$id_form = $args[0];
	$id_donnee = $args[1];
	$ordre = _request('ordre');
	$rang_nouv = 0;

	include_spip('inc/autoriser');
	// cas ou on a passe une liste d'id ordonnes
	if ($ordre){
		$table_sort = explode("&",$ordre);
		$last_rang = 0;
		foreach($table_sort as $item){
			$item = explode("=",$item);
			$item = explode("-",end($item));
			$donnees[] = reset($item);
			$rangs[] = end($item);
			if (($n = count($rangs))>=2){
				if ($rangs[$n-1]<$rangs[$n-2]){ // irregularite
					if ($n>=3){
						if ($rangs[$n-1]<$rangs[$n-3]) 
							{$id_donnee = $donnees[$n-1];$rang_nouv = $rangs[$n-2];}
						else
							{$id_donnee = $donnees[$n-2];$rang_nouv = $rangs[$n-1];}
					}
					else
						{$id_donnee = $donnees[$n-2];$rang_nouv = $rangs[$n-1];}
					continue;
				}
			}
		}
		if ($rang_nouv)
			if (autoriser('modifier','donnee',$id_donnee,NULL,array('id_form'=>$id_form))){
				include_spip("base/forms_base_api_v2");
				forms_ordonner_donnee($id_donnee,$rang_nouv);
			}
	}
	else {
		if (autoriser('modifier','donnee',$id_donnee,NULL,array('id_form'=>$id_form))){
			$rang_nouv = _request('rang_nouv');
			include_spip("base/forms_base_api_v2");
			forms_ordonner_donnee($id_donnee,$rang_nouv);
		}
	}
}

?>