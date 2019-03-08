<?php
/**
  Plugin SPIPr-Dane-Config
  Fichier #FORMULAIRE_COULEURS
  * formulaire de configuration des 3 couleurs de base
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3

*/
include_spip('inc/config');

 function lire_variables_couleur_less(){
    // on regarde si le fichier colors.less est dans le rep squelettes/css du site
    // sinon, on va chercher celui de SPIPr-Dane
    if( !is_file( $file_less =_DIR_SITE."squelettes/css/colors.less") )
         $file_less = _DIR_PLUGIN_SPIPR_DANE."/css/colors.less";
     if( file( $file_less ) ) 
        $lines = file( $file_less );

 	return $lines;
 }

 function variables_couleurs(){
 	return "/^(@color1:|@color2:|@color3:)/";
 }

function formulaires_couleurs_charger_dist( $bloc ) {
	// 
	$meta='sdc/';
	// on charge les saisies et les champs qui nécessitent un accès par les fonctions
	$valeurs = array(
		'bloc' => $bloc,
		'color1' => lire_config($meta.$bloc.'/color1', "#DF0D46"), 
		'color2' => lire_config($meta.$bloc.'/color2', "#455C98"), 
		'color3' => lire_config($meta.$bloc.'/color3', "#DFDFDF") 
	);

	return $valeurs;
}

function formulaires_couleurs_verifier_dist( $bloc ) {
	$erreurs = array();
/*
	Placer ici les controles sur les champs
*/
    if( !$lines = lire_variables_couleur_less() ) 
        $erreurs['message_erreur']="lire_variables_couleur_less absent";
    
    if (!is_dir(_DIR_SITE."squelettes/css/")) 
        if (!mkdir(_DIR_SITE."squelettes/css/", 0755, true)) 
            $erreurs['message_erreur'] ='Echec lors de la création des répertoires '._DIR_SITE.'squelettes/css/';

    return $erreurs;
}

function formulaires_couleurs_traiter_dist( $bloc ) {
	// Traitement des données reçues du formulaire, 
	$meta='sdc/';
	$vals=array('color1'=>'#DF0D46', 'color2'=>'#455C98', 'color3'=>'#DFDFDF');

	if( !_request('_cfg_delete' )){
		// Ecriture des valeurs dans colors.less
	 	if( $lines = lire_variables_couleur_less() ) {
			foreach( $lines as $line_num => $line ) {
				if( preg_match(variables_couleurs(), $line, $matches) ){
					$name = preg_replace("/@|:/", '', $matches[0]);
					$data .= $matches[0]." \t".strtoupper(_request($name)).";\n";
				}
				else {
					$data .= $line;
				}
			}
            //enregistrement de colors.less dans le rep squelettes/css du site
			file_put_contents(_DIR_SITE."squelettes/css/colors.less", $data);
	 	}

        // enregistrement des valeurs dans spip_meta 
		foreach($vals as $nom => $valeur) {
			if(  _request($val)!='' ){
				ecrire_config($meta.$bloc.'/'.$nom, _request($nom));
				if( is_null(lire_config($meta.$bloc.'/'.$nom)) )
					$errs.= $nom;
			}
			else {
			effacer_config($meta.$bloc.'/'.$val);
			}
		}
	}
	else {
        foreach($vals as $nom => $valeur) {
		  effacer_config($meta.$bloc.'/'.$nom);
        }
	 	if( $lines = lire_variables_couleur_less() ) {
			foreach( $lines as $line_num => $line ) {
				if( preg_match(variables_couleurs(), $line, $matches) ){
					$name = preg_replace("/@|:/", '', $matches[0]);
					$data .= $matches[0]." \t".strtoupper($vals[$name]).";\n";
				}
				else 
					$data .= $line;
            }
            //enregistrement de colors.less dans le rep squelettes/css du site
			file_put_contents(_DIR_SITE."squelettes/css/colors.less", $data);
        }
        else
 		return array('message_erreur'=>"lire_variables_couleur_less absent");
           
        set_request('color1', "#DF0D46");
        set_request('color2', "#455C98");
        set_request('color3', "#DFDFDF");
        //unlink(_DIR_SITE."squelettes/css/colors.less"); // On supprime le fichier squelettes/css/colors.less.

        return array('message_ok'=>_T('sdc:params_couleurs_supprimes') );
	}
	// S'il y a des erreurs, elles sont retournées au formulaire
	if( $errs != $errDefaut )
		return array('message_erreur'=>_T('sdc:params_couleurs_non_enregistres'));
	  
	// Sinon, le message de confirmation est envoyé
	else 
		return array('message_ok'=>_T('sdc:params_couleurs_enregistres'));
}
?>