<?php
/**
  Plugin SPIPr-Dane-Config
  Fichier #FORMULAIRE_BACKGROUND
  * formulaire de configuration des styles css d'arriere plan des blocs 
  * param string : bloc - bloc a configurer (casier)
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3
*/
include_spip('inc/config');

function formulaires_background_charger_dist( $bloc ) {
// on charge les saisies et les champs qui nécessitent un accès par les fonctions
    $valeurs = array(
        'bloc' => $bloc,
        'bgimg' =>  !is_null(lire_config('sdc/'.$bloc.'/background-image') )? 'on' : '', 
        'background-color' => lire_config('sdc/'.$bloc.'/background-color', 'transparent'), 
        'background-image' => lire_config('sdc/'.$bloc.'/background-image'), 
        'background-repeat' => lire_config('sdc/'.$bloc.'/background-repeat', 'repeat'), 
        'background-position' => lire_config('sdc/'.$bloc.'/background-position', 'top left'), 
        'background-attachment' => lire_config('sdc/'.$bloc.'/background-attachment'),
        'background-size' => lire_config('sdc/'.$bloc.'/background-size', 'auto'),
        'largeur_background' => lire_config('sdc/'.$bloc.'/largeur_background') 
);

    return $valeurs;
}


function formulaires_background_verifier_dist( $bloc ) {
    $erreurs = array();
/*
	Placer ici les controles sur les champs
*/
    if (_request('bgimg') == 'on' && _request('background-image') == '')
        $erreurs['background-image'] = _T('sdc:background_image_erreur');
    
    if (_request('background-image') != '') {
        if ( !preg_match("/.(png|gif|jpg|jpeg)$/", _request('background-image'))) 
            $erreurs['background-image'] = _request('background-image')._T('sdc:background_image_erreur_ext');

        if ( !preg_match("/([a-zA-Z0-9]|_|-|.)/", _request('background-image')))
            $erreurs['background-image'] = _request('background-image')._T('sdc:background_image_erreur_nom');
    }
    if (_request('background-position') != '') {
        if ( !preg_match("/^(top|right|bottom|left|center)$/", _request('background-position'))
        && !preg_match("/(top|right|bottom|left|center)$/", _request('background-position'))){
            if ( !preg_match("/^[0-9]{1,3}/", _request('background-position')) 
            && !preg_match("/(em|px|%)$/", _request('background-position')))
                 $erreurs['background-position'] = _T('sdc:background_position_erreur');
        }
    }
    if (_request('background-size') != '') {
        if ( !preg_match("/^(cover|contain|auto)$/", _request('background-size'))
        && !preg_match("/(cover|contain|auto)$/", _request('background-size'))){
            if ( !preg_match("/^[0-9]{1,3}(em|px|%)$/", _request('background-size')) 
            && !preg_match("/(em|px|%)$/", _request('background-size')))
                 $erreurs['background-size'] = _T('sdc:background_size_erreur');
        }
    }

    return $erreurs;
}

function formulaires_background_traiter_dist( $bloc ) {
    //Traitement des données reçues du formulaire, 
    $titre=array('header'=>'de l\'entête', 'body'=>'de la page', 'footer'=>'du pied de page');
    $vals=array('img'=>array('background-image','background-repeat','background-position','background-attachment','background-size',));
    if (!_request('_cfg_delete')){
        if ( _request('background-color')!='' )
            ecrire_config('sdc/'.$bloc.'/background-color', _request('background-color'));
        else 
            effacer_config('sdc/'.$bloc.'/background-color');

        if ( _request('largeur_background')!='' && $bloc="header") 
            ecrire_config('sdc/'.$bloc.'/largeur_background', _request('largeur_background'));
        else 
            effacer_config('sdc/'.$bloc.'/largeur_background');

        foreach($vals['img'] as $val) {
            if ( _request('bgimg') =="on" &&  _request($val)!=''){
                ecrire_config('sdc/'.$bloc.'/'.$val, _request($val));
                if(is_null(lire_config('sdc/'.$bloc.'/'.$val)))
                    $errs.=   $val.' = '._request($val).'<br/>';
                else 
                    $oks.=  $val.' = '._request($val).'<br/>';
            }
            else {
                effacer_config('sdc/'.$bloc.'/'.$val);
            }
        }
    }
    else {
        foreach($vals['img'] as $val) {
            effacer_config('sdc/'.$bloc.'/'.$val);
            set_request($val, '');
        }
        effacer_config('sdc/'.$bloc.'/bgimg');
        effacer_config('sdc/'.$bloc.'/background-color');
        set_request('bgimg', '');
        set_request('background-color', '');
        if($bloc=="header"){
            effacer_config('sdc/'.$bloc.'/largeur_background');
            set_request('background-color', '');
        }

        return array('message_ok'=> _T('sdc:params_background_supprimes', array('bloc'=>$titre[$bloc])));
	}
   
    // S'il y a des erreurs, elles sont retournées au formulaire
    if($errs)
        return array('message_erreur'=>_T('sdc:params_background_non_enregistres', array('bloc'=>$titre[$bloc])));
  
    // Sinon, le message de confirmation est envoyé
    else 
        return array('message_ok'=> _T('sdc:params_background_enregistres', array('bloc'=>$titre[$bloc])));
}
?>
