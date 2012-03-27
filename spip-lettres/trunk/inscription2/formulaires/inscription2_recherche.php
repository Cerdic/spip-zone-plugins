<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Chargement des valeurs par defaut des champs du formulaire
 * 
 */
function formulaires_inscription2_recherche_charger_dist(){
	
	$datas['ordre'] = _request('ordre');
	$datas['desc'] = _request('desc');
	$datas['case'] = _request('case');
	$datas['valeur'] = _request('valeur');
	
	if(_request('afficher_tous')){
		set_request('valeur','');
		set_request('case','');
	}
	return $datas;
}

/**
 * 
 * Vérification du formulaire
 * @return 
 */
function formulaires_inscription2_recherche_verifier_dist(){
	global $visiteur_session;
	
	if(_request('supprimer_auteurs')){
		$auteurs_checked = _request('check_aut');
		if(is_array($auteurs_checked)){
			include_spip('inc/autoriser');
			foreach($auteurs_checked as $key=>$val){
				$statut = sql_getfetsel('statut','spip_auteurs','id_auteur='.intval($val));
				if(!autoriser('modifier','auteur',$val) || ($statut == '0minirezo')){
					$erreurs['check_aut'.$val] = true;
					spip_log("pas autorisé");
				}
			}
			if(count($erreurs)>0){
				$erreurs['message_erreur'] = _T('inscription2:suppression_comptes_impossible');
			}
		}else{
			$erreurs['message_erreur'] = _T('inscription2:no_user_selected');
		}
	}
	
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

/**
 * 
 * Traitement du formulaire
 * @return 
 */
function formulaires_inscription2_recherche_traiter_dist(){
	
	$retour = array();
	if(_request('supprimer_auteurs')){
		$auteurs_checked = _request('check_aut');
		$nb_auteurs = 0;
		if(is_array($auteurs_checked)){
			foreach($auteurs_checked as $key=>$val){
				$statut = sql_getfetsel('statut','spip_auteurs','id_auteur='.intval($val));
				if($statut !='0minirezo') {
					sql_updateq("spip_auteurs",array('statut' => '5poubelle'),"id_auteur=".intval($val));
					sql_delete("spip_auteurs_elargis","id_auteur=".intval($val));
							
					if(defined('_DIR_PLUGIN_ACCESRESTREINT'))
						sql_delete("spip_zones_auteurs","id_auteur=".intval($val));
			
					if(defined('_DIR_PLUGIN_SPIPLISTES'))
						sql_delete("spip_auteurs_listes","id_auteur=".intval($val));
					$nb_auteurs++;
				}
			}
		}else{
			// Rien à faire
		}
		$retour['message_ok'] = _T('inscription2:nb_users_supprimes',array('nb'=>$nb_auteurs));
	}
    return $retour;
}
?>
