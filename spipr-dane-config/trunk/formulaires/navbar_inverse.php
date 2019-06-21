<?php
/**
  Plugin SPIPr-Dane-Config
  * Fichier #FORMULAIRE_NAVBAR_INVERSE
  * formulaire de configuration de la couleur de la barre de nav 
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3

*/

// securite
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}
include_spip('inc/config');

function lire_variables_barnav_less ($fichier) {
    if (!is_file( $file_less =_DIR_SITE."squelettes/css/".$fichier.".less")) {
        $file_less = _DIR_PLUGIN_SPIPR_DANE."/css/".$fichier.".less";
    }
    if ($lines = file($file_less)) {
        return $lines;
    }
    else {
        return false;
    }
}

function formulaires_navbar_inverse_charger_dist() {
// on charge les saisies et les champs qui nécessitent un accès par les fonctions
    $valeurs = array(
    	'inverser_couleur' => lire_config('sdc/navbar/inverser_couleur') == 'color1' ? 'on': '',
        'couleur_liens' => lire_config('sdc/navbar/couleur_liens', 'white'),
        'couleur_liens_hover' => lire_config('sdc/navbar/couleur_liens_hover'),
        'scrolltofixed' => lire_config('sdc/navbar/scrolltofixed')
    );
    
    return $valeurs;
}


function formulaires_navbar_inverse_verifier_dist() {
	$erreurs = array();
    //on verifie que le rep squelette/css existe
    // sinon on le cree
    if (!is_dir(_DIR_SITE."squelettes/css/")) {
        if (!mkdir(_DIR_SITE."squelettes/css/", 0755, true)) {
            $erreurs['inverser_couleur'] =_T('sdc:erreur_creer_dir_css', array('dir'=>_DIR_SITE));
        }
    }
	return $erreurs;
}

function formulaires_navbar_inverse_traiter_dist() {
    // Traitement des données reçues du formulaire, 
    $couleur_lien_hover = _request('couleur_liens_hover') ? (_request('couleur_liens')=="white"?"black":"white") : _request('couleur_liens');
    $inverser_couleur = _request("inverser_couleur");
    is_array(_request("inverser_couleur")) ? set_request('inverser_couleur', 'color1') : set_request('inverser_couleur', 'color2');
    $couleur_background_hover=_request('inverser_couleur')&&_request('inverser_couleur')=="color1"?"color2":"color1";
    $errs ='';

	if (!_request('_cfg_delete')){
        if ( _request('couleur_liens') && _request('couleur_liens') !='' &&  _request('inverser_couleur') && _request('inverser_couleur') !=''){
            // Ecriture de la couleur des liens dans colors.less
            if ($lines = lire_variables_barnav_less("colors")) {
                foreach ($lines as $line) {
                    if (preg_match("#^(@navbarBackground:)#", $line, $matches)) {
                        $data .= $matches[0]." \t@"._request('inverser_couleur').";\n";
                    }
                    else if (preg_match("#^(@navbarLinkColor:)#", $line, $matches)) {
                        $data .= $matches[0]." \t@"._request('couleur_liens').";\n";
                    }
                    else if (preg_match("#^(@navbarLinkColorHover:)#", $line, $matches)) {
                        $data .= $matches[0]." \t@".$couleur_lien_hover.";\n";
                    }
                    else if (preg_match("#^(@navbarLinkBackgroundHover:)#", $line, $matches)) {
                        $data .= $matches[0]." \t@".$couleur_background_hover.";\n";
                    }
                    else {
                        $data .= $line;
                    }
                }

                //enregistrement de colors.less dans le rep squelettes/css du site
                if (!file_put_contents(_DIR_SITE."squelettes/css/colors.less", $data)) {
                    $errs=_T('sdc:erreur_enregistrement_couleur_barnav');
                }
            }
            // ecriture des metas
            ecrire_config('sdc/navbar/couleur_liens', _request('couleur_liens'));
            ecrire_config('sdc/navbar/couleur_liens_hover', _request('couleur_liens_hover'));
            ecrire_config('sdc/navbar/inverser_couleur', _request('inverser_couleur'));
            ecrire_config('sdc/navbar/scrolltofixed', _request('scrolltofixed'));
        }
        if ($inverser_couleur) {
            set_request("inverser_couleur", $inverser_couleur);
        }
	}
	else {
        if ($lines = lire_variables_barnav_less("colors")) {
            foreach ($lines as $line) {
                if (preg_match("#^(@navbarBackground:)#", $line, $matches)) {
                    $data .= $matches[0]." \t@color2;\n";
                }
                else if (preg_match("#^(@navbarLinkColor:)#", $line, $matches)) {
                    $data .= $matches[0]." \t@white;\n";
                }
                else if (preg_match("#^(@navbarLinkColorHover:)#", $line, $matches)) {
                    $data .= $matches[0]." \t@navbarLinkColor;\n";
                }
                else if (preg_match("#^(@navbarLinkBackgroundHover:)#", $line, $matches)) {
                    $data .= $matches[0]." \t@color1;\n";
                }
                else {
                    $data .= $line;
                }
            }
            //enregistrement de colors.less dans le rep squelettes/css du site
			file_put_contents(_DIR_SITE."squelettes/css/colors.less", $data);
        }
        effacer_config('sdc/navbar/inverser_couleur');
        effacer_config('sdc/navbar/couleur_liens');
        effacer_config('sdc/navbar/couleur_liens_hover');
        effacer_config('sdc/navbar/scrolltofixed');
        set_request('couleur_liens', 'white');
        set_request('couleur_liens_hover', '');
        
        $oks = _T('sdc:params_barnav_supprimes');
        
		return array('message_ok'=>$oks);
	}
   
  // S'il y a des erreurs, elles sont retournées au formulaire
  if( $errs !='' ) {
      return array('message_erreur'=>_T('sdc:params_barnav_non_enregistres'));
  }

  // Sinon, le message de confirmation est envoyé
  else {
      return array('message_ok'=>_T('sdc:params_barnav_enregistres'));
  }
}
