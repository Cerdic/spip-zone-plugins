<?php

function prang_formulaire_charger($flux) {

    switch ($flux['args']['form']) {
        case 'editer_rubrique' :
        case 'editer_article' :
            if ($flux['data']['rang']) {
                $flux['data']['titre'] = $flux['data']['rang'].'. '.$flux['data']['titre'];         
            }
            break;
    }
    return $flux;
}

function prang_formulaire_verifier($flux) {
        
    switch ($flux['args']['form'] ) {
        case 'editer_rubrique' :
        case 'editer_article' :
            $array = extraire_rang(_request('titre'));
            set_request('titre',$array['titre']);
            set_request('rang',$array['rang']);
            break;
    }
    return $flux;
}



//ajouter la valeur de rang dans la base
function prang_formulaire_traiter($flux) {
        
    switch($flux['args']['form']) {
        case 'editer_rubrique' :
            update_rang(_request('rang'),'rubrique',$flux['data']['id_rubrique']);
            break;
        case 'editer_article' :
            update_rang(_request('rang'),'article',$flux['data']['id_article']);
            break;
    }
    
    return $flux;
}

function update_rang($rang,$objet,$id_objet) {

    switch($objet) {
        case 'rubrique' :
            $id_table = 'id_rubrique';
            break;
        case 'article' :
            $id_table = 'id_article';
            break;
    }

    sql_updateq("spip_".$objet."s", array('rang' => $rang), "id_".$objet.'='.$id_objet);    
}

function extraire_rang($texte) {
    list($rang,$titre) = explode(".", $texte, 2);
    $rang = trim($rang);
    $titre = trim($titre);
    
    if (!is_numeric($rang)) {
        $rang = null;
        $titre = $texte;
    }
    
    return array('rang'=>$rang,'titre'=>$titre);
}

?>
