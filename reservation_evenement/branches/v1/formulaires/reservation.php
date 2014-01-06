<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_reservation_charger_dist($id='',$id_article=''){

	
	// si pas d'evenement ou d'inscription, on echoue silencieusement
	
	$where=array('date_fin>NOW() AND inscription=1 AND statut="publie"');
    if($id){
        if(!is_array($id))array_push($where,'id_evenement='.intval($id));
        elseif(is_array($id))array_push($where,'id_evenement IN ('.implode(',',$id).')');
        }
    if($id_article){
        if(!is_array($id_article)) array_push($where,'id_article='.intval($id_article));   
        elseif(is_array($id_article))array_push($where,'id_article IN '.implode(',',$id_article).')');
        }

	$sql = sql_select('*','spip_evenements',$where,'','date_debut,date_fin');

    $evenements=array();
    $articles=array();
    while ($row=sql_fetch($sql)){
        $evenements[$row['id_evenement']]=$row;
        $articles[]=$row['id_article'];
    }
    $valeurs = array('evenements'=>$evenements,'articles'=>$evenements);

    
    if(intval($GLOBALS['visiteur_session'])){
        $session=$GLOBALS['visiteur_session'];
        $nom=$session['nom'];
        $email=$session['email'];                
        
    }

	// valeurs d'initialisation
	$valeurs['id_evenement'] = _request('id_evenement')?(
	   is_array(_request('id_evenement'))
	       ?_request('id_evenement'):array(_request('id_evenement')))
       :array();
       
    $valeurs['id_objet_prix'] = _request('id_objet_prix')?(
       is_array(_request('id_objet_prix'))
           ?_request('id_objet_prix'):array(_request('id_objet_prix')))
       :array();
          
       
    $valeurs['id_auteur']=$id_auteur; 
    $valeurs['nom']=$nom; 
    $valeurs['email']=$email; 
    $valeurs['enregistrer']=_request('enregistrer');  
    $valeurs['new_pass']=_request('new_pass');    
    $valeurs['new_pass2']=_request('new_pass2');  
    $valeurs['new_login']=_request('new_login');       
	$valeurs['statut'] = 'encours'; 
	   
    //les champs extras auteur
    include_spip('cextras_pipelines');
    
    if(function_exists('champs_extras_objet')){
        //Charger les définitions pour la création des formulaires
        $valeurs['champs_extras_auteurs']=champs_extras_objet(table_objet_sql('auteur'));
       foreach($valeurs['champs_extras_auteurs'] as $key =>$value){
           $valeurs[$value['options']['nom']]=$session[$value['options']['nom']];
           $valeurs['champs_extras_auteurs'][$key]['options']['label']=extraire_multi($value['options']['label']);    
           
            }
        }
    
   
    $valeurs['_hidden'].='<input type="hidden" name="statut" value="'.$valeurs['statut'].'"/>'; 
   


	return $valeurs;
}

function formulaires_reservation_verifier_dist($id='',$id_article=''){
	$erreurs = array();
    $email=_request('email');
    
    if(isset($GLOBALS['visiteur_session']['id_auteur'])){
                $id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
            }
        
         if(_request('enregistrer'))  {
            include_spip('inc/auth');
             $obligatoires=array('nom','email','new_pass','new_login');
             foreach($obligatoires AS $champ){
                   if(!_request($champ))$erreurs[$champ]=_T("info_obligatoire");
                  }
            //Vérifier le login
            if ($err = auth_verifier_login($auth_methode, _request('new_login'), $id_auteur)){
                $erreurs['new_login'] = $err;
                $erreurs['message_erreur'] .= $err;
            }
             
              //Vérifier les mp
             if ($p = _request('new_pass')) {
                    if ($p != _request('new_pass2')) {
                        $erreurs['new_pass'] = _T('info_passes_identiques');
                        $erreurs['message_erreur'] .= _T('info_passes_identiques');
                    }
                    elseif ($err = auth_verifier_pass($auth_methode, _request('new_login'),$p, $id_auteur)){
                        $erreurs['new_pass'] = $err;
                    }
                }
             }
        else{
            $obligatoires=array('nom','email');
            
        if(_request('id_objet_prix'))array_push($obligatoires,'id_objet_prix');
        else  array_push($obligatoires,'id_evenement');
       
            foreach($obligatoires AS $champ){
                if(!_request($champ))$erreurs[$champ]=_T("info_obligatoire");
            }
        } 

         if ($email){
            include_spip('inc/filtres');
            // un redacteur qui modifie son email n'a pas le droit de le vider si il y en avait un
            if (!email_valide($email)){
                $id_auteur_session=isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:'';
                $erreurs['email'] = (($id_auteur==$id_auteur_session)?_T('form_email_non_valide'):_T('form_prop_indiquer_email'));
                }
            elseif(!$id_auteur){
                if($email_utilise=sql_getfetsel('email','spip_auteurs','email='.sql_quote($email))) $erreurs['email']=_T('reservation:erreur_email_utilise');
                }
            }
         
    //les champs extras auteur
    include_spip('cextras_pipelines');
    
    if(function_exists('champs_extras_objet')){
        include_spip('inc/saisies');
        //Charger les définitions pour la création des formulaires
        $champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
        $erreurs=array_merge($erreurs,saisies_verifier($champs_extras_auteurs));
        }

	return $erreurs;
}

function formulaires_reservation_traiter_dist($id='',$id_article=''){
    include_spip('inc/session');    
    include_spip('inc/config');
    $config=lire_config('reservation_evenement');
    $statut = $config['statut_defaut']?$config['statut_defaut']:'attente'; 

    //Créer la réservation
    $action=charger_fonction('editer_objet','action');
    // La référence
    $fonction_reference = charger_fonction('reservation_reference', 'inc/');
    if(isset($GLOBALS['visiteur_session']['id_auteur']))$id_auteur=$GLOBALS['visiteur_session']['id_auteur'];  
   $set=array('statut'=>$statut);
    
    //les champs extras auteur
    include_spip('cextras_pipelines');
    $valeurs_extras=array();
    if(function_exists('champs_extras_objet')){
        //Charger les définitions pour la création des formulaires
        $champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
       foreach( $champs_extras_auteurs as $value){
             $valeurs_extras[$value['options']['nom']]=_request($value['options']['nom']); 
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