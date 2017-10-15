<?php
/*-----------------------------
Traitement du formulaire de choix du statut
-------------------------------*/
function formulaires_info_balai_charger_dist($statut_balai, $type, $id) {
    $valeurs = array(
        'statut_balai' => $statut_balai,
    );
    return $valeurs;
}


function formulaires_info_balai_verifier_dist($statut_balai, $type, $id){
    $erreurs = array();
    if (!_request('statut_balai')){
        $erreurs['message_erreur'] = "Cette information est obligatoire";
    } else {
        if (!in_array(_request('statut_balai'),
            array('protege', 'non_protege', 'protege_permanent'))){
                $erreurs['message_erreur'] = "Valeur incorrecte";
            }
    }
    return $erreurs;
}


function formulaires_info_balai_traiter_dist($statut_balai, $type, $id, $id_auteur){
    $reponse = array();

    // Pas de changement de statut
    if (_request('statut_balai') == $statut_balai){
        $reponse ['message_ok'] = '';
        return $reponse;
    }


    //Passage à non protégé : on supprime l'entrée de la table spip_balai
    if (_request('statut_balai') == 'non_protege'){
        sql_delete("spip_balai", "objet = ". sql_quote($type) . " AND id_objet = $id");
        $reponse ['message_ok'] = "";
        return $reponse;
    }

    $time = time();
    $now = date('Y-m-d H:i:s', $time);
    sql_insertq("spip_balai", array(
                "objet" => $type,
                "id_objet" => $id,
                "id_auteur" =>$id_auteur,
                "date" => $now
                )
            );

    $reponse ['message_ok'] = "";
    return $reponse;
}
?>
