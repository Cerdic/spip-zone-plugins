<?php

function pb_couleur_rubrique_header_prive($flux){
    $flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_PB_COULEUR_RUBRIQUE  .'javascript/script.js"></script>';
    return $flux;
}

function pb_couleur_rubrique_affiche_droite($flux) {
	
        $exec = $flux["args"]["exec"];
       
        if ($exec == "rubrique") {
            $id_rubrique = $flux["args"]["id_rubrique"];
            $contexte = array('id_rubrique'=>$id_rubrique);
            $flux["data"] .= recuperer_fond("inclure/couleur_rubrique", $contexte);
        }
     
        return $flux;
    }


?>