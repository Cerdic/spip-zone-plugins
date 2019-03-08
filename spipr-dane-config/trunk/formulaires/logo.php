<?php
/*
  Plugin SPIPr-Dane-Config
  Fichier #FORMULAIRE_LOGO
  * formulaire de configuration d'affichage du logo.
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3

*/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/config');

//
// Charger
// 
function formulaires_logo_charger_dist($bloc){
	// definition des valeurs de base du formulaire
	$valeurs = array(
		'bloc'=>$bloc,
		'masquer_logo' => lire_config('sdc/'.$bloc.'/masquer_logo'), 
		'position_logo' => lire_config('sdc/'.$bloc.'/position_logo'), 
		'largeur_logo' => lire_config('sdc/'.$bloc.'/largeur_logo', '300'), 
		'position_logo_acad' => lire_config('sdc/'.$bloc.'/position_logo_acad'), 
	);
	return $valeurs;
}

//
// Verifier
// 
function formulaires_logo_verifier_dist($bloc,$elem){
	$erreurs = array();
    if(!is_int(intval(_request('largeur_logo'))))
        $erreurs['largeur_logo']="Vous devez saisir un nombre entier";
    
	return $erreurs;
}

//
// Traiter
// 
function formulaires_logo_traiter_dist($bloc){
	$res = array('editable'=>' ', 'message_ok'=>'', 'message_erreur'=>'');
	$vals=array('masquer_logo'=>'','position_logo'=>'','largeur_logo'=>'300','position_logo_acad'=>'');

	if (!_request('_cfg_delete') ){
		foreach($vals as $champ => $val) {
			if (_request($champ)!=''){
				ecrire_config('sdc/'.$bloc.'/'.$champ, _request($champ));
				if(is_null(lire_config('sdc/'.$bloc.'/'.$champ)))
					$res['message_erreur'].=   $champ.' = '._request($champ).'<br/>';

            }
			else 
				effacer_config('sdc/'.$bloc.'/'.$champ);
		}
        $res['message_ok']= "La configuration des logos a été enregistrée";
	}
    else {
        foreach($vals as $champ => $val) {
            effacer_config('sdc/'.$bloc.'/'.$champ);
            set_request($champ, $val);
        }
        $res['message_ok']= "La configuration des logos a été supprimée";
    }
	return $res;
}

?>