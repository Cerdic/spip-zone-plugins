<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function syncro_spip_mc($liste_spip,$liste_mc,$derniere_syncro){
	spip_log('syncro_spip_mc','slcp');
		$donnes_liste_spip=array();

	while($data=sql_fetch($liste_spip)){
		
		$donnes_liste_spip[$data['email']]=$data;

		}
	spip_log($donnes_liste_spip,'slcp');
	}


function syncro_mc_spip($liste_spip,$liste_mc,$derniere_syncro){
	$donnes_liste_spip=array();

	while($data=sql_fetch($liste_spip)){
		
		$donnes_liste_spip[$data['email']]=$data;

		}

	spip_log($donnes_liste_spip,'slcp');
	foreach($liste_mc['data'] AS $membre){



		}
	}
	
// Syncroniser des listes
function syncroniser_listes($api='',$id_liste_spip,$id_liste_mailchimp,$status='',$start='',$limit=''){

	include_spip('squirrel_chimp_lists_fonctions');
	
	//pour ecrire_config 
	include_spip('inc/config');
	
	//L'api de mailchimps
	include_spip('inc/1.3/MCAPI.class');

	
	// initialisation d'un objet mailchimp	
	if(!$api){
		$apikey=lire_config('squirrel_chimp/apiKey');
		$api = new MCAPI($apikey);
		}
	
	// la date de la dernière syncro générale
	$since=sql_getfetsel('date_syncro','spip_listes_syncro','objet="listes" AND type_syncro="generale" AND id_objet='.$id_liste_spip,'','date_syncro DESC');


	// lesn données spip
	$champs=champs_spip();
	$champs_spip_auteurs=array();
	foreach($champs AS $champ){
		$champs_spip_auteurs[]='spip_auteurs.'.$champ;
		}
		
	$c=implode(',',$champs_spip_auteurs).',spip_auteurs_listes.maj,spip_auteurs_listes.date_syncro,spip_auteurs_listes.statut,spip_auteurs.email,spip_auteurs.id_auteur,spip_auteurs.id_mailchimp';	
	
	$liste_spip=sql_select($c,'spip_auteurs_listes,spip_auteurs','spip_auteurs_listes.id_liste='.$id_liste_spip.' AND spip_auteurs_listes.id_auteur=spip_auteurs.id_auteur  AND spip_auteurs_listes.date_syncro >'.sql_quote($since));
	
	// Composer le tableau distinguant entre abonnées et désabonnées
	$listes_spip=array();
	while($data=sql_fetch($liste_spip)){
		if($data['email'])$listes_spip[$data['statut']][$data['email']]=$data;
		}
	
	//Les listes mc distinguant entre abonnées et désabonnées
	if(!$status){
		$statuts=array('subscribed','unsubscribed');
		$listes_mc=array();
		foreach($statuts AS $status){
			$listes_mc[$status]=membres_liste_mc($api,$id_liste_mailchimp,$status,$since,$limit);
			}	
		}
	else {
		$liste_mc=membres_liste_mc($api,$id_liste_mailchimp,$status,$since,$limit);
		}
	
	//Etablir les candiats à la syncro.

	//D'abord les désinscriptions
	$liste_desabonnement=array();
	$liste_abonnement=array();
	$liste_traites_mc=array();
	$timestamp_desabo=array();
	
	foreach($listes_mc['unsubscribed']['data'] as $info_m_mc){
		$syncro_spip='';
		if($syncro_spip=$listes_spip['valide'][$info_m_mc['email']]['maj']){		
			if($info_m_mc['timestamp'] > $syncro_spip){
				$liste_desabonnement['vers_spip'][]=$listes_spip['valide'][$info_m_mc['email']]['id_auteur'];
				$timestamp_desabo[$listes_spip['valide'][$info_m_mc['email']]['id_auteur']]=$info_m_mc['timestamp'] ;
			}
			// on le réinscrir sur mc
			elseif($syncro_spip > $info_m_mc['timestamp']){
				$liste_abonnement['vers_mc'][$info_m_mc_2['email']]=$listes_spip['a_valider'][$info_m_mc_2['email']];
				}
			}
		}	

	//spip_log($listes_spip,'slcp');		
	//spip_log($liste_desabonnement,'slcp');		
	//spip_log($listes_mc['subscribed']['data'],'slcp');
			
	//Ensuite les inscriptions et actualisations	

	foreach($listes_mc['subscribed']['data'] as $info_m_mc_2){
		$syncro_spip_2='';
		//spip_log($listes_spip['valide'][$info_m_mc_2['email']].$info_m_mc_2['email'],'slcp');	

		//L'inscris mc est désactivé en spip
				
		if($syncro_spip_2=$listes_spip['a_valider'][$info_m_mc_2['email']]['maj']){
			//spip_log('1.1','slcp');			
				if($info_m_mc_2['timestamp'] > $syncro_spip_2){
					//spip_log('1.1.1','slcp');	

					//on cherche les données de l'abonnée mc
					$info=membres_liste_info_mc($api,$id_liste_mailchimp,$info_m_mc_2['email']);
					 $liste_abonnement['vers_spip'][$info_m_mc_2['email']]=$info['data'][0];
				 }
				elseif($syncro_spip_2 > $info_m_mc_2['timestamp']){
					$liste_desabonnement['vers_mc'][]=$info_m_mc_2['email'];
					$timestamp_desabo[$listes_spip['valide'][$info_m_mc_2['email']]['id_auteur']]=$info_m_mc_2['timestamp'] ;
					//spip_log('1.1.2','slcp');	
					}
				}
			//spip_log($info_m_mc_2['timestamp'], 'slcp');						
		//L'inscris mc est active en spip mais date d'actualisation plus anciennes que celle de mc				
		elseif(($syncro_spip_2=$listes_spip['valide'][$info_m_mc_2['email']]['maj'])){
			//spip_log('1.2','slcp');	
				if($info_m_mc_2['timestamp'] > $syncro_spip_2){					 
					 //on cherche les données de l'abonnée mc
					 $info=membres_liste_info_mc($api,$id_liste_mailchimp,$info_m_mc_2['email']);
					 $liste_abonnement['vers_spip'][$info_m_mc_2['email']]=$info['data'][0];
					 }
				elseif($syncro_spip_2 > $info_m_mc_2['timestamp']){
				 		$liste_abonnement['vers_mc'][$info_m_mc_2['email']]=$listes_spip['valide'][$info_m_mc_2['email']];
				 		}
				}
			//Présent sur mailchhimp 	
		else{
			//mais pas abonné à la liste spip ou pas de modifications depuis la dernière syncro	
			if($listes_spip['valide'][$email]){
				//on cherche les données de l'abonnée mc
				$info=membres_liste_info_mc($api,$id_liste_mailchimp,$info_m_mc_2['email']);
				$liste_abonnement['vers_spip'][$info_m_mc_2['email']]=$info['data'][0];
				}
			}	
		$liste_traites_mc[$info_m_mc_2['email']]=$info_m_mc_2['timestamp'];		
		}
		
	
	// On récupère les abbonées spip pas encore traitées	
	$a_traiter_abo=array_diff_key($listes_spip['valide'],$liste_traites_mc);

	foreach ($a_traiter_abo AS $email=>$data){
		$liste_abonnement['vers_mc'][$email]=$listes_spip['valide'][$email];
		}
		

	
	// On sycronise
	
	// les variables
	$optin = lire_config('squirrel_chimp/ml_opt_in')?false:true; //yes, send optin emails
	$up_exist = true; // yes, update currently subscribed users
	$replace_int = false; // no, add interest, don't replace
	
	// Désabonnement	
	if($liste_desabonnement){
		if($liste_desabonnement['vers_mc']){		
			desinscription_batch_mc($api,$id_liste_mailchimp,$liste_desabonnement['vers_mc']);
			}
		elseif($liste_desabonnement['vers_spip']){
			desinscription_batch_spip($id_liste_spip,$liste_desabonnement['vers_spip'],$timestamp_desabo);
			}
		}
		
		
	
	// Inscriptions	
	if($liste_abonnement){
		if($liste_abonnement['vers_mc']){
			$batch=donnees_sync_simple($id_liste_mailchimp,$liste_abonnement['vers_mc']);		
			inscription_batch_mc($api,$id_liste_mailchimp,$batch,$optin,$up_exist,$replace_ints);
			}
		elseif($liste_abonnement['vers_spip']){
			inscription_batch_spip($id_liste_spip,$liste_abonnement['vers_spip']);
			}
		}
		

	//$nombre_liste_spip=sql_count($liste_spip);
	//$nombre_liste_mc=count($liste_mc);
	
	/*if($nombre_liste_spip>=$nombre_liste_mc)$sync=syncro_spip_mc($liste_spip,$liste_mc,$derniere_syncro);
	else $sync=syncro_mc_spip($liste_spip,$liste_mc,$derniere_syncro);*/
	//spip_log('a traiter'.serialize($a_traiter), 'slcp');	
	//spip_log($listes_spip['valide'], 'slcp');
	//spip_log($listes_spip['a_valider'], 'slcp');
	spip_log($liste_abonnement, 'slcp');
	spip_log($batch, 'slcp');
	spip_log($timestamp_desabo, 'slcp');
	//spip_log($liste_desabonnement, 'slcp');
	//spip_log($listes_spip, 'slcp');
	spip_log($desabonner_mc, 'slcp');
	
	//spip_log($liste_traites_mc, 'slcp');	

	
	//spip_log($data, 'slcp');
	//spip_log("Admin $messageErreur",'squirrel_chimp');
	return;
	
}

// Prépare les données pour la synchronisation
function donnees_sync_simple($id_liste,$donnees){
	
	// Les champs spip à traiter
	$champs_sync=champs_pour_concordance($id_liste);
	

	// Préparation de l'array a envoyer à mailchimp
	$batch=array();
	$i=0;
	foreach($donnees AS $key=>$data){
		echo serialize($data);
		if(!is_array($data))$data =$donnees;
		$i++;
		foreach($data AS $key=>$value){
			if(!is_array($champs_sync[$key])){
				if(array_key_exists($key,$champs_sync))$batch[$i][$champs_sync[$key]]=$value;
				}
			}
		}
		
	return $batch;
}


// Désinscription en masse des abonnées
function desinscription_batch_spip($id_liste,$ids,$timestamp_desabo){
	
	foreach($ids as $id){
		$val=array('statut'=>'a_valider','maj'=>$timestamp_desabo[$id],'date_syncro'=>$timestamp_desabo[$id]);
		sql_updateq('spip_auteurs_listes',$val,'id_liste='.$id_liste);
		}
	return;
	}
	
// Inscription en masse dans spip
function inscription_batch_spip($id_liste_spip,$batch){
	
	foreach($ids as $id){
		$val=array('statut'=>'a_valider','maj'=>$timestamp_desabo[$id],'date_syncro'=>$timestamp_desabo[$id]);
		sql_updateq('spip_auteurs_listes',$val,'id_liste='.$id_liste);
		}
	return;
	}
	

/* Récuperer des infos des membres d'une liste MailChimp
 *  listMembers(string apikey, string id, string status, string since, int start, int limit)
 * http://apidocs.mailchimp.com/api/rtfm/listmembers.func.php
 */
 
function membres_liste_mc($api='',$id_liste_mailchimp,$status='subscribed',$since='',$start=0,$limit=15000){
	
	//pour ecrire_config 
	include_spip('inc/config');
	
	//L'api de mailchimps
	include_spip('inc/1.3/MCAPI.class');
	
	// initialisation d'un objet mailchimp	
	if(!$api){
		$apikey=lire_config('squirrel_chimp/apiKey');
		$api = new MCAPI($apikey);
		}
	
	$retval = $api->listMembers($id_liste_mailchimp,$status,$since,$start,$limit);
	
	
	return $retval;
	
}

/* Les informations d'un abonné MailChimp
 * listMemberInfo(string apikey, string id, array email_address)
 * http://apidocs.mailchimp.com/api/1.3/listmemberinfo.func.php
 */
 
function membres_liste_info_mc($api='',$id_liste,$email=''){
	//pour ecrire_config 
	include_spip('inc/config');
	
	//L'api de mailchimps
	include_spip('inc/1.3/MCAPI.class');
	
	if(!$api){
		$apikey=lire_config('squirrel_chimp/apiKey');
		$api = new MCAPI($apikey);		
		}
	
	$retval = $api->listMemberInfo($id_liste,$email);
//spip_log($retval, 'slcp');

	return $retval;
	
}


/* Les informations d'un abonné MailChimp
 * listBatchUnsubscribe(string apikey, string id, array emails, boolean delete_member, boolean send_goodbye, boolean send_notify)
 * http://apidocs.mailchimp.com/api/rtfm/listbatchunsubscribe.func.php
 */
 
function desinscription_batch_mc($api='',$id_liste,$email,$delete_member=false,$send_goodby=false,$send_notify=false){
	//pour ecrire_config 
	include_spip('inc/config');
	
	//L'api de mailchimps
	include_spip('inc/1.3/MCAPI.class');
	
	if(!$api){
		$apikey=lire_config('squirrel_chimp/apiKey');
		$api = new MCAPI($apikey);		
		}
	
	$retval = $api->listBatchUnsubscribe($api,$id_liste,$email,$delete_member,$send_goodby,$send_notify);
//spip_log($retval, 'slcp');

	return $retval;
	
}
/* Les informations d'un abonné MailChimp
 * listBatchSubscribe(string apikey, string id, array batch, boolean double_optin, boolean update_existing, boolean replace_interests)
 * http://apidocs.mailchimp.com/api/rtfm/listbatchsubscribe.func.php
 */
 
function inscription_batch_mc($api,$id_liste,$batch,$optin,$update=true,$replace_interests=false){
	//pour ecrire_config 
	include_spip('inc/config');
	
	//L'api de mailchimps
	include_spip('inc/1.3/MCAPI.class');
	
	if(!$api){

		$apikey=lire_config('squirrel_chimp/apiKey');
		$api = new MCAPI($apikey);		
		}
	
	$retval = $api->listBatchSubscribe($id_liste,$batch,$optin,$update,$replace_interests);
	spip_log($retval, 'slcp');

	return $retval;
	
}






?>
