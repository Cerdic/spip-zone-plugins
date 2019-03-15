<?php
/**
  Plugin SPIPr-Dane-Config
  Fichier #FORMULAIRE_TYPOGRAPHY
  * formulaire de configuration des polices de caracteres
  * param string : bloc - bloc a configurer (casier) [defaut|body|header|...]
  * Les polices sont importees depuis ggogle fonts https://fonts.googleapis.com
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3
*/
include_spip('inc/config');
include_spip('inc/yaml');

 function lire_variables_typo_less(){
    // on regarde si le fichier typogtaphy.less est dans le rep squelettes/css du site
    // sinon, on va chercher celui de SPIPr-Dane
    if( !is_file( $file_less =_DIR_SITE."squelettes/css/typography.less") )
         $file_less = _DIR_PLUGIN_SPIPR_DANE."/css/typography.less";
     if( file( $file_less ) ) 
        $lines = file( $file_less );

 	return $lines;
 }

function formulaires_typography_charger_dist( $bloc ) {
	// 
	$meta = 'sdc/';
    $font = lire_config('sdc/'.$bloc.'/font-family', 'Open Sans');
    // tableau des polices google
    // import depuis le fichier gg_fonts.yaml
    $polices=yaml_decode_file(find_in_path('yaml/gg_fonts.yaml'));
    
    foreach($polices as $famille){
        if( in_array( lire_config('sdc/'.$bloc.'/font-family'), $famille ) && $font == "")
            $font=lire_config('sdc/'.$bloc.'/font-family');
    }
    
    if ( $font == "") $font="perso";
	// on charge les saisies et les champs qui nécessitent un accès par les fonctions
	$valeurs = array(
		'bloc' => $bloc,
        'polices' => yaml_decode_file(find_in_path('yaml/gg_fonts.yaml')),
		'font-family' => $font, 
		'font-family-perso' => $font == "perso" ? lire_config('sdc/'.$bloc.'/font-family') : '' ,
		'font-size' => lire_config('sdc/'.$bloc.'/font-size', '2.6'), 
		'color' => lire_config('sdc/'.$bloc.'/color', lire_config('sdc/defaut/color2','#455C98')), 
	);
	return $valeurs;
}

function formulaires_typography_verifier_dist( $bloc ) {
	$erreurs = array();
/*
	Placer ici les controles sur les champs
*/
    if (!is_dir(_DIR_SITE."squelettes/css/")) {
        if (!mkdir(_DIR_SITE."squelettes/css/", 0755, true)) {
            $erreurs['file'] ='Echec lors de la création des répertoires '._DIR_SITE."squelettes/css/";
        }
    }

    return $erreurs;
}

function formulaires_typography_traiter_dist( $bloc ) {
	//
	$meta='sdc/';
	// Traitement des données reçues du formulaire, 
    // Est-ce une font perso ?
	if ( _request('font-family') == "perso" &&  _request('font-family-perso') && _request('font-family-perso') !="")
		set_request("font-family", _request('font-family-perso'));
    else 
        set_request("font-family-perso", '');
	$vals=array('font-family');
    $nom_bloc=array('defaut'=>'de base','title'=>'du titre');
    if ( $bloc == "title")
        $vals=array('font-family', 'font-size', 'color'); 
    $errs='';$oks = '';
    

	if (!_request('_cfg_delete')){
		
		// Ecriture des valeurs dans typography.less
        if ($lines =  lire_variables_typo_less()) {
            foreach ($lines as $line_num => $line) {
                if (_request("font-family")) {
                    if (preg_match("/^@sansFontFamily:/", $line, $matches) &&  $bloc != "title")
                        $data .= $matches[0]." \t"."'"._request("font-family")."',Arial,Helvetica,sans-serif;\n";
                    else if (preg_match("/^(@import|\/\/@import)/", $line, $matches)) {
                        $importFonts = preg_replace('/\s/', '+', trim(_request("font-family"))); 
                        if ( $bloc != "title" && lire_config($meta.'title/font-family'))
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'title/font-family'))); 
                        else if ( $bloc == "title" && lire_config($meta.'defaut/font-family'))
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'defaut/font-family'))); 
                            
                        if (_request("font-family") == "Open Sans")
                            //$data .= "//@import url('https://fonts.googleapis.com/css?family=".$importFonts."');\n" ;
                            $data .= $line;
                        else
                            $data .= "@import url('https://fonts.googleapis.com/css?family=".$importFonts."');\n" ;
                    } else 
                        $data .= $line;
                }
                else 
                    $data .= $line;
            }
            //enregistrement du fichier typography.less dans squelettes/css
            file_put_contents(_DIR_SITE."squelettes/css/typography.less", $data);
        }
        

		foreach($vals as $val) {
			if (  _request($val)!='' ) {
				ecrire_config($meta.$bloc.'/'.$val, _request($val));
				if(is_null(lire_config($meta.$bloc.'/'.$val)))
					$errs.=   $val.' = '._request($val).'<br/>';
				else 
					$oks.=  $val.' = '._request($val).'<br/>';
			}
			else {
                effacer_config($meta.$bloc.'/'.$val);
                if ( $bloc != "title")
                    unlink (_DIR_SITE."squelettes/css/typography.less");
			}
		}
	   if ( _request('font-family-perso') && _request('font-family-perso') !="")
           set_request("font-family", "perso");

	}
	else {
        if ( ( $bloc=="defaut" && !lire_config('sdc/title/font-family') ) || ( $bloc=="title" && !lire_config('sdc/defaut/font-family') ) )
            unlink(_DIR_SITE."squelettes/css/typography.less");
        if ($bloc=="defaut")
            $importFonts = preg_replace('/\s/', '+', trim(lire_config($meta.'title/font-family')));
        else if ($bloc=="title")
            $importFonts = preg_replace('/\s/', '+', trim(lire_config($meta.'defaut/font-family')));
        
        if ($lines =  lire_variables_typo_less()) {
            foreach ($lines as $line_num => $line) {
                if(preg_match("/^@sansFontFamily:/", $line, $matches) && $bloc=="defaut"){
                    $data .= $matches[0]." \t"."'Open Sans',Arial,Helvetica,sans-serif;\n";
                }
                else if (preg_match("/^@import/", $line, $matches)) {
                     $data .= "@import url('https://fonts.googleapis.com/css?family=".$importFonts."');\n" ;
                }
                else 
                    $data .= $line;
            }
        }
        //enregistrement du fichier typography.less dans squelettes/css
        file_put_contents(_DIR_SITE."squelettes/css/typography.less", $data);
      
        foreach($vals as $val) {
            effacer_config($meta.$bloc.'/'.$val);
        }
        set_request('font-family', 'Open Sans');
        if ( $bloc == "title"){
            set_request('font-size', '2.6');
            set_request('color', lire_config('sdc/defaut/color2', '#455C98'));
        }
		$oks = _T('sdc:params_typography_supprimes', array('bloc'=>$nom_bloc[$bloc])); 
		return array('message_ok'=>$oks);
	}
	// S'il y a des erreurs, elles sont retournées au formulaire
	if( $errs != $errDefaut )
		return array('message_erreur'=> _T('sdc:params_typography_non_enregistres', array('bloc'=>$nom_bloc[$bloc])));
	  
	// Sinon, le message de confirmation est envoyé
	else 
		return array('message_ok'=> _T('sdc:params_typography_enregistres', array('bloc'=>$nom_bloc[$bloc])));
}  
?>