<?php

function prang_formulaire_charger($flux) {

    if ($flux['args']['form'] == 'editer_rubrique') {
        $flux['data']['titre'] = $flux['data']['rang'].'. '.$flux['data']['titre']; 
    }
    
    return $flux;
}

function prang_formulaire_verifier($flux) {
    
    if ($flux['args']['form'] == 'editer_rubrique') {
        list($rang,$titre) = explode(".", _request('titre'), 2);
        if (is_numeric(trim($rang))) {
            set_request('titre',trim($titre));
            set_request('rang',trim($rang));
        }
    }
    return $flux;
}



//ajouter la valeur de rang dans la base
function prang_formulaire_traiter($flux) {
    
    //var_export($flux['data']['id_rubrique']);
    
    if ($flux['args']['form'] == 'editer_rubrique') {
        sql_updateq('spip_rubriques', array('rang' => _request('rang')), 'id_rubrique='.$flux['data']['id_rubrique']);
    }
    
    return $flux;
}
?>
