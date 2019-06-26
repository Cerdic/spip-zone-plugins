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

	// tableau des polices google
    // import depuis le fichier gg_fonts.yaml
    $polices = yaml_decode_file(find_in_path('yaml/gg_fonts.yaml'));
    $listeFonts = array();

    foreach($polices as $famille) {
        $listeFonts = array_merge($listeFonts, $famille);
    }
    if(!in_array( $font, $listeFonts) || lire_config($meta.$bloc.'/font-weight')) {
        $fontFamilyPerso = $font;
        $font = 'perso';
		if (lire_config($meta.$bloc.'/font-weight')) {
            $fontFamilyPerso .= ":".lire_config($meta.$bloc.'/font-weight');
		}
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
            $erreurs['font-family'] ='Echec lors de la création des répertoires '._DIR_SITE."squelettes/css/";
        }
    }
    
   return $erreurs;
}

function formulaires_typography_traiter_dist($bloc) {
    // Traitement des données reçues du formulaire, 
    $meta = 'sdc/';
    $fontFamily = _request('font-family');
    $fontFamilyPerso = _request('font-family-perso');
	effacer_config($meta.$bloc.'/font-weight');
    
    // Est-ce une font perso ?
    if ($fontFamily == "perso" &&  $fontFamilyPerso && $fontFamilyPerso !="") {
        $fontFamily = $fontFamilyPerso;
    }
    else {
        set_request("font-family-perso", '');
    }
    
    if (preg_match("/:/",$fontFamilyPerso)) {
        $font = explode(":", $fontFamilyPerso);
        $fontFamily = $font[0];
        $fontWeight = $font[1];
    }

 
    $vals=array('font-family');
    $nom_bloc = array('defaut'=>'de base','title'=>'du titre','h2'=>'des titres des blocs');
    if ($bloc == "title") {
        $vals = array('font-family', 'font-size', 'font-weight', 'color'); 
    }
    $errs = $oks = '';

    if (!_request('_cfg_delete') && $fontFamily) {
        // Ecriture des valeurs dans typography.less
        if ($lines =  lire_variables_typo_less()) { 
            foreach ($lines as $line_num => $line) {
                if (preg_match("/^@sansFontFamily:/", $line, $matches) &&  $bloc != "title" &&  $bloc != "h2") {
                    $data .= $matches[0]." \t"."'".$fontFamily."',Arial,Helvetica,sans-serif;\n";
                }
                else if (preg_match("/^(@import|\/\/@import)/", $line, $matches)) {
                    $importFonts = preg_replace('/\s/', '+', trim($fontFamily));
                    if ($fontWeight) {
						$regular = preg_match('/i$/',$fontWeight) ? ':400i,' : ':400,';
                        $importFonts .= $regular.$fontWeight;
                    }
                           
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

                    if ($fontFamily == "Open Sans") {
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
            if ($bloc == 'h2') {
                $bloc='body/h2';
            }
            ecrire_config($meta.$bloc.'/font-family', $fontFamily);
			if ($bloc == 'title' || $bloc == 'body/h2') {
                _request('font-size')!='' ? ecrire_config($meta.$bloc.'/font-size', _request('font-size')) : effacer_config($meta.$bloc.'/font-size') ;
                _request('color')!='' ? ecrire_config($meta.$bloc.'/color', _request('color')) : effacer_config($meta.$bloc.'/color') ;
                // font-weight
                if (isset($fontWeight) && $fontWeight != '') {
					$fontWeight = preg_replace('/i$/','',$fontWeight);
					ecrire_config($meta.$bloc.'/font-weight', $fontWeight);
				} 
				else {
					effacer_config($meta.$bloc.'/font-weight');
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
		
		return array('message_ok'=>_T('sdc:params_typography_supprimes', array('bloc'=>$nom_bloc[$bloc])));
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
