<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_jeux_gerer_resultats_saisies($param=array()){
    if($param['id_auteur']){
        $label_faire = _T('jeux_gerer_resultats:pour_auteur');   
    }
    elseif($param['id_jeu']){
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

function formulaires_jeux_gerer_resultats_charger($param=array()){
    $param['saisies'] = formulaires_jeux_gerer_resultats_saisies($param);
    return $param;
}
function formulaires_jeux_gerer_resultats_verifier($param=array()){
    $erreurs = array();
    if (!_request('confirmer') and _request('faire')){
        $erreurs['non_confirme']=true;
    }
    return $erreurs;
}
function formulaires_jeux_gerer_resultats_traiter($param=array()){
    $faire = _request('faire');
    $id_auteur  =   $param['id_auteur'];
    $id_jeu     =   $param['id_jeu'];
    // Supprimer
    if ($faire == 'supprimer'){
        if($param['id_auteur']){
            sql_delete('spip_jeux_resultats', "id_auteur=$id_auteur");  
        }
        elseif($id_jeu){
            sql_delete('spip_jeux_resultats', "id_jeu=$id_jeu");
        }
        else{
            sql_delete('spip_jeux_resultats');   
        }  
    }
    


    return $param;
}
?>