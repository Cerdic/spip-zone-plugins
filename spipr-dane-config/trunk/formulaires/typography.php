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

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}
include_spip('inc/config');
include_spip('inc/yaml');


function lire_variables_typo_less() {
    // on regarde si le fichier typogtaphy.less est dans le rep squelettes/css du site
    // sinon, on va chercher celui de SPIPr-Dane
    if (!is_file( $file_less =_DIR_SITE."squelettes/css/typography.less")) {
        $file_less = _DIR_PLUGIN_SPIPR_DANE."/css/typography.less";
    }
    if (file( $file_less )) {
        $lines = file( $file_less );
    }

    return $lines;
}

function formulaires_typography_charger_dist($bloc) {
    // 
    $meta = 'sdc/';

    if (!$font = lire_config($meta.$bloc.'/font-family')) {
        if (!$font = lire_config($meta.'defaut/font-family')) {
            $font = 'Open Sans';
        }
    }
    /*
     if ($bloc == 'title' && !lire_config($meta.'title/font-family')) {
        $font = lire_config($meta.'defaut/font-family') ;
    }
    */
   // tableau des polices google
    // import depuis le fichier gg_fonts.yaml
    $polices = yaml_decode_file(find_in_path('yaml/gg_fonts.yaml'));
    $listeFonts = array();

    foreach($polices as $famille) {
        $listeFonts = array_merge($listeFonts, $famille);
    }
    if(!in_array( $font, $listeFonts)) {
        $fontFamilyPerso = $font;
        $font = 'perso';
    }
    // on charge les saisies et les champs qui nécessitent un accès par les fonctions
    $valeurs = array(
        'bloc' => $bloc,
        'polices' => yaml_decode_file(find_in_path('yaml/gg_fonts.yaml')),
        'font-family' => $font, 
        'font-family-perso' => $fontFamilyPerso,
        'font-size' => lire_config('sdc/'.$bloc.'/font-size', '2.6'), 
        'color' => lire_config('sdc/'.$bloc.'/color', lire_config('sdc/defaut/color2','#455C98')), 
    );
    
    return $valeurs;
}

function formulaires_typography_verifier_dist($bloc) {
    $erreurs = array();
    
    // on verifie que le rep squelettes/css existe sinon on le cree
    if (!is_dir(_DIR_SITE."squelettes/css/")) {
        if (!mkdir(_DIR_SITE."squelettes/css/", 0755, true)) {
            $erreurs['file'] ='Echec lors de la création des répertoires '._DIR_SITE."squelettes/css/";
        }
    }

    return $erreurs;
}

function formulaires_typography_traiter_dist($bloc) {
    // Traitement des données reçues du formulaire, 
    $meta = 'sdc/';
    
    // Est-ce une font perso ?
    if (_request('font-family') == "perso" &&  _request('font-family-perso') && _request('font-family-perso') !="") {
        set_request("font-family", _request('font-family-perso'));
    }
    else {
        set_request("font-family-perso", '');
    }

    $vals=array('font-family');
    $nom_bloc = array('defaut'=>'de base','title'=>'du titre','h2'=>'des titres des blocs');
    if ($bloc == "title") {
        $vals = array('font-family', 'font-size', 'color'); 
    }
    $errs = $oks = '';

    if (!_request('_cfg_delete') && _request("font-family")) {
        // Ecriture des valeurs dans typography.less
        if ($lines =  lire_variables_typo_less()) { 
            foreach ($lines as $line_num => $line) {
                if (preg_match("/^@sansFontFamily:/", $line, $matches) &&  $bloc != "title" &&  $bloc != "h2") {
                    $data .= $matches[0]." \t"."'"._request("font-family")."',Arial,Helvetica,sans-serif;\n";
                }
                else if (preg_match("/^(@import|\/\/@import)/", $line, $matches)) {
                    $importFonts = preg_replace('/\s/', '+', trim(_request("font-family")));
                           
                    if ( $bloc == "defaut" ) {
                        if (lire_config($meta.'title/font-family')) {
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'title/font-family')));
                        }
                        if (lire_config($meta.'h2/font-family')) {
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'h2/font-family'))); 
                        }
                    }
                    else if ($bloc == "title") {
                        if ( lire_config($meta.'defaut/font-family')) {
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'defaut/font-family')));
                        }
                        if (lire_config($meta.'h2/font-family') && !preg_match("/".lire_config($meta.'h2/font-family')."/", $line)) {
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'h2/font-family')));
                        }
                    }
                    else if ($bloc == "h2"){
                        if (lire_config($meta.'defaut/font-family')) {
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'defaut/font-family')));
                        }
                        if (lire_config($meta.'title/font-family') && !preg_match("/".lire_config($meta.'title/font-family')."/", $line)) {
                            $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'title/font-family')));
                        }
                    }

                    if (_request("font-family") == "Open Sans") {
                        $data .= $line;
                    }
                    else {
                        $data .= "@import url('https://fonts.googleapis.com/css?family=".$importFonts."&display=swap');\n";
                    } 
                }
                else {
                    $data .= $line;
                }
            }
            //enregistrement du fichier typography.less dans squelettes/css
            file_put_contents(_DIR_SITE."squelettes/css/typography.less", $data);
            
            //ecriture dans spip_metas
            foreach ($vals as $val) {
                if ($bloc == 'h2') {
                    $bloc='body/h2';
                }
                if (_request($val)!='' ) {
                    ecrire_config($meta.$bloc.'/'.$val, _request($val));
                    if (is_null(lire_config($meta.$bloc.'/'.$val))) {
				        $errs.= 'Erreur dans la meta'  .$val.' = '._request($val);
                    }
                }
                else {
                    effacer_config($meta.$bloc.'/'.$val);
                    if ($bloc == "defaut" && !lire_config($meta.'title/'.$val) && !lire_config($meta.'body/h2/'.$val)) {
                        unlink (_DIR_SITE."squelettes/css/typography.less");
                    }
                }
            }
            if ( _request('font-family-perso') && _request('font-family-perso') !="") {
                set_request("font-family", "perso");
            }
        }
	}
    else {
        //if (($bloc=="defaut" && !lire_config('sdc/title/font-family')) || ($bloc=="title" && !lire_config('sdc/defaut/font-family'))) {
        //    unlink(_DIR_SITE."squelettes/css/typography.less");
        //}
        if ($bloc == "defaut") {
            if (!lire_config('sdc/title/font-family') && !lire_config('sdc/body/h2/font-family')) {
                unlink(_DIR_SITE."squelettes/css/typography.less");
            }
            else {
                if (lire_config('sdc/title/font-family')) {
                    $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'title/font-family')));
                }
                if (lire_config('sdc/body/h2/font-family')) {
                    $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'body/h2/font-family')));
                }
            }
        }
        else if ($bloc == "title") {
            if (lire_config($meta.'defaut/font-family')) {
                $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'defaut/font-family')));
            }
            if (lire_config($meta.'body/h2/font-family')) {
                $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'body/h2/font-family')));
            }
        }
        else if ($bloc == "h2") {
            if (lire_config($meta.'defaut/font-family')) {
                $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'defaut/font-family')));
            }
            if (lire_config($meta.'title/font-family')) {
                $importFonts .= '|'.preg_replace('/\s/', '+', trim(lire_config($meta.'title/font-family')));
            } 
        }

        if ($lines = lire_variables_typo_less()) {
            foreach ($lines as $line_num => $line) {
                if (preg_match("/^@sansFontFamily:/", $line, $matches) && $bloc=="defaut") {
                    $data .= $matches[0]." \t"."'Open Sans',Arial,Helvetica,sans-serif;\n";
                }
                else if (preg_match("/^@import/", $line, $matches)) {
                    $importFonts = preg_replace('/^\|/', '', $importFonts);
                    $data .= "@import url('https://fonts.googleapis.com/css?family=".$importFonts."');\n" ;
                }
                else {
                    $data .= $line;
                }
            }
        }
        // enregistrement du fichier typography.less dans squelettes/css
        file_put_contents(_DIR_SITE."squelettes/css/typography.less", $data);
      
        foreach($vals as $val) {
            if($bloc=='h2') {
                effacer_config($meta.'body/h2/'.$val);
            }
            else {
                effacer_config($meta.$bloc.'/'.$val);
            }
        }
        // on renvoie les valeurs par defaut
        set_request('font-family', lire_config($meta.'defaut/font-family, Open Sans'));
        if ($bloc == "title") {
            set_request('font-size', '2.6');
            set_request('color', lire_config('sdc/defaut/color2', '#455C98'));
        }
		$oks = _T('sdc:params_typography_supprimes', array('bloc'=>$nom_bloc[$bloc])); 
		return array('message_ok'=>$oks);
	}
    // S'il y a des erreurs, elles sont retournées au formulaire
    if( $errs != '' ) {
        return array('message_erreur'=> _T('sdc:params_typography_non_enregistres', array('bloc'=>$nom_bloc[$bloc])));
    }
    // Sinon, le message de confirmation est envoyé
	else {
		return array('message_ok'=> _T('sdc:params_typography_enregistres', array('bloc'=>$nom_bloc[$bloc])));
    }
}  
