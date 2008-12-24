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

function action_forms_lier_donnees_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	$cherche_donnee = _request('cherche_donnee');

	$id_donnee_liee = _request('id_donnee_liee'); // peut etre un tableau du fait d'un select multiple
	if (!$id_donnee_liee) $id_donnee_liee = intval(_request('_id_donnee_liee'));
	if (!is_array($id_donnee_liee)) $id_donnee_liee = array($id_donnee_liee);
	// securisons
	$id_donnee_liee = array_map('intval',$id_donnee_liee);

	$args = explode(",",$args);
	$id = intval($args[0]);
	$type = $args[1];
	if (!preg_match(',[\w]+,',$type))
		$type = 'article';
	$faire = $args[2];
	
	$redirect = _request('redirect');
	if (!$redirect) $redirect = "";
	
	include_spip('inc/autoriser');
	if ($faire=='ajouter'){
		if ($id!=0 && $id_donnee_liee && ($id<0 OR autoriser('modifier',$type,$id))){
			$champ_donnee = 'id_donnee';
			if ($type=='donnee') 
				$champ_donnee = 'id_donnee_liee';
			$deja = array_map('reset',sql_allfetsel($champ_donnee,"spip_forms_donnees_{$type}s","id_$type=".intval($id)." AND ".sql_in($champ_donnee,$id_donnee_liee)));
			$id_donnee_liee = array_diff($id_donnee_liee,$deja);
			if (count($id_donnee_liee)){
				$ins = array();
				foreach($id_donnee_liee as $id_liee)
					$ins[] = array("id_$type"=>$id,$champ_donnee=>$id_liee);
				sql_insertq_multi("spip_forms_donnees_{$type}s",$ins);
			}
			$redirect = parametre_url($redirect,'cherche_donnee','');
		}
		// a virer avec cvt je crois
		elseif (!$id_donnee_liee){
			if ($cherche_donnee)
				$redirect = parametre_url($redirect,'cherche_donnee',$cherche_donnee);
			$redirect = parametre_url($redirect,'ajouter','1');
		}
	}
	elseif ($faire=='retirer'){
		$id_donnee_liee = intval($args[3]);
		if ($id && $id_donnee_liee && autoriser('modifier',$type,$id)){
			$champ_donnee = 'id_donnee';
			if ($type=='donnee') 
				$champ_donnee = 'id_donnee_liee';
			sql_delete("spip_forms_donnees_{$type}s","id_$type=".intval($id)." AND $champ_donnee=".intval($id_donnee_liee));
		}
	}
	
	include_spip('inc/headers');
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>