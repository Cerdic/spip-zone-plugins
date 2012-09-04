<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_jeux_gerer_resultats_saisies($param=array()){
    if (isset($param['id_auteur']) and $param['id_auteur']){
        $label_faire = _T('jeux_gerer_resultats:pour_auteur');   
    }
    elseif (isset($param['id_jeu']) and $param['id_jeu']){
        $label_faire = _T('jeux_gerer_resultats:pour_jeu');   
    }
    else{
        $label_faire = _T('jeux_gerer_resultats:pour_tous');   
    }
    $saisies = array(
        array('saisie'=>'radio',
                'options'=>array(
                    'nom'=>'faire',
                    'obligatoire'=>'oui',
                    'datas'=>array(
                        'compacter'=>_T('jeux_gerer_resultats:compacter'),
                        'supprimer'=>_T('jeux_gerer_resultats:supprimer')
                    ),
                    'label'=>$label_faire
                )
            ));
    return $saisies;
}

function formulaires_jeux_gerer_resultats_charger_dist($param=array(),$return=''){
    if (!autoriser('gerer','resultats')){
        return false;   
    }
    $param['saisies'] = formulaires_jeux_gerer_resultats_saisies($param);
    return $param;
}
function formulaires_jeux_gerer_resultats_verifier_dist($param=array(),$return=''){
    $erreurs = array();
    if (!_request('confirmer') and _request('faire')){
        $erreurs['non_confirme']=true;
    }
    return $erreurs;
}
function formulaires_jeux_gerer_resultats_traiter_dist($param=array(),$return=''){
    $faire = _request('faire');
    $id_auteur  =   $param['id_auteur'];
    $id_jeu     =   $param['id_jeu'];
    
    if ($return){
        $param['redirect']=$return;  
    }
    // Supprimer
    if ($faire == 'supprimer'){
        if($id_auteur){
            sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur");  
        }
        elseif($id_jeu){
            sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu");
        }
        else{
            sql_delete('spip_jeux_resultats');   
        }
        $param['message_ok']=_T('jeux_gerer_resultats:resultats_supprimes');  
    }
    // Compacter
    if ($faire == 'compacter'){
        if($id_auteur){ 
            formulaire_gerer_resultats_compacter_auteur($id_auteur);
        }
        elseif($id_jeu){
            formulaire_gerer_resultats_compacter_jeu($id_jeu);
        }
        else{
            $auteurs = sql_select('id_auteur','spip_jeux_resultats','',array('id_auteur')); // tout les auteurs
            while ($auteur = sql_fetch($auteurs)){
              formulaire_gerer_resultats_compacter_auteur($auteur['id_auteur']);     
            }
        }
        $param['message_ok']=_T('jeux_gerer_resultats:resultats_compactes');
    }
    
    return $param;
}

function formulaire_gerer_resultats_compacter_auteur($id_auteur){
    $liste = array();
    $jeux  = sql_select('id_jeu','spip_jeux_resultats',"id_auteur=$id_auteur",array('id_jeu')); // jeu où l'auteur à un résultats
    while ($jeu=sql_fetch($jeux)){
            $id_jeu=$jeu['id_jeu'];
            $liste[] = sql_getfetsel('id_resultat','spip_jeux_resultats',"id_auteur=$id_auteur AND id_jeu=$id_jeu",'',"date DESC");
    }
    sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur AND ".sql_in('id_resultat', $liste, 'NOT')); 
}
function formulaire_gerer_resultats_compacter_jeu($id_jeu){
    $liste = array();
    $auteurs  = sql_select('id_auteur','spip_jeux_resultats',"id_jeu=$id_jeu",array('id_auteur')); // auteur où l'auteur à un résultats
    while ($auteur=sql_fetch($auteurs)){
        $id_auteur=$auteur['id_auteur'];
        $liste[] = sql_getfetsel('id_resultat','spip_jeux_resultats',"id_auteur=$id_auteur AND id_jeu=$id_jeu",'',"date DESC");
    }
    sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu AND ".sql_in('id_resultat', $liste, 'NOT'));   
}
?>
