<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

//Enregistrement d'une réservation
function inc_reservation_enregistrer_dist($id='',$id_article=''){
 include_spip('inc/session');    
    include_spip('inc/config');
    $config=lire_config('reservation_evenement');
    $statut = $config['statut_defaut']?$config['statut_defaut']:'rien';
	if($statut=='rien'){
		$statut_defaut=charger_fonction('defaut','inc/statut');
		$statut=$statut_defaut($statut);
	} 

    //Créer la réservation
    $action=charger_fonction('editer_objet','action');
	
    // La référence
    $fonction_reference = charger_fonction('reservation_reference', 'inc/');
    if(isset($GLOBALS['visiteur_session']['id_auteur']))$id_auteur=$GLOBALS['visiteur_session']['id_auteur'];  
   $set=array('statut'=>$statut,'lang'=>_request('lang'));
    
    //les champs extras auteur
    include_spip('cextras_pipelines');
    $valeurs_extras=array();
    if(function_exists('champs_extras_objet')){
        //Charger les définitions pour la création des formulaires
        $champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
       foreach( $champs_extras_auteurs as $value){
             $valeurs_extras[$value['options']['label']]=_request($value['options']['nom']); 
            }
        }

   if(_request('enregistrer')){
            include_spip('actions/editer_auteur');
            
            if(!$id_auteur){
                $res = formulaires_editer_objet_traiter('auteur','new','','',$retour,$config_fonc,$row,$hidden);
                $id_auteur=$res['id_auteur'];
                sql_updateq('spip_auteurs',array('statut'=>'6forum'),'id_auteur='.$id_auteur);
                }
        
        $set['reference']=$fonction_reference($id_auteur);
        }
   elseif(!intval($id_auteur)){
       $set['nom']=_request('nom');
       $set['email']=_request('email'); 
       $set['donnees_auteur']=serialize( $valeurs_extras);
   }
   else{
       $valeurs=array_merge(array('nom'=>_request('nom'),'email'=>_request('email')),$valeurs_extras);
       sql_updateq('spip_auteurs',$valeurs,'id_auteur='.$id_auteur);
        
   }
    $set['reference']=$fonction_reference();      
    $set['id_auteur']=$id_auteur;
	
    $id_reservation=$action('new','reservation',$set);
    $message='<p>'._T('reservation:reservation_enregistre').'</p>';
    $message.='<h3>'._T('reservation:details_reservation').'</h3>';
    $message.=recuperer_fond('inclure/reservation',array('id_reservation'=>$id_reservation[0]));
    
    //Ivalider les caches
    include_spip('inc/invalideur');
    suivre_invalideur("id='reservation/$id_reservation'");
    suivre_invalideur("id='reservations_detail/$id_reservations_detail'");	
	return array('message_ok'=>$message,'editable'=>false);
}

?>
