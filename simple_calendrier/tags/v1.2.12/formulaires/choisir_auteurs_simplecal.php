<?php
// Fichier PHP du formulaire CVT.

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_choisir_auteurs_simplecal_charger_dist($id_evenement){
    $valeurs = array(
        'ch_num'=>'',
        'id_evenement'=>$id_evenement
    );
    
    return $valeurs;
}


function formulaires_choisir_auteurs_simplecal_verifier_dist($id_evenement){
    $retour = array();
        
    // clic sur le bouton ajouter ou retirer
    if (_request('ajouter') || _request('retirer')){
    
        // Champ non renseigné
        if (!_request('ch_num')) {
            $retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
            $retour['ch_num'] = _T('simplecal:auteur_msg_num_manquant');
        } 
        // Champ renseigné
        else {
            // id de l'auteur saisi
            $id_auteur = trim(_request('ch_num'));
            
            // La saisie n'est pas un nombre
            if ($id_auteur != "".intval($id_auteur)){
                $retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
                $retour['ch_num'] = _T('simplecal:auteur_msg_num_incorrect');
            } 
            
            // La saisie est un nombre
            else {
                // Récupération de l'auteur
                $auteur = sql_fetsel("id_auteur" ,"spip_auteurs", "id_auteur=".$id_auteur);
                
                // L'auteur n'existe pas
                if (!$auteur){
                    $retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
                    $retour['ch_num'] = _T('simplecal:auteur_msg_id_inexistant', array('id_auteur'=>$id_auteur));
                } 
                // L'auteur existe
                else {
                    // Est-il déjà rattaché à l'évènement ?
                    $est_rattache = sql_countsel('spip_auteurs_evenements', "id_evenement=".$id_evenement." and id_auteur = ".$auteur['id_auteur']);
                    
                    // Oui, et dans le cas d'un ajout, on provoque une erreur
                    if ($est_rattache > 0 && _request('ajouter')){
                        $retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
                        $retour['ch_num'] = _T('simplecal:auteur_msg_id_dejala', array('id_auteur'=>$id_auteur));
                    } 
                    
                    // Non, et dans le cas d'une suppression, on provoque une erreur
                    else if ($est_rattache == 0 && _request('retirer')){
                        $retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
                        $retour['ch_num'] = _T('simplecal:auteur_msg_id_pasauteur', array('id_auteur'=>$id_auteur));
                    }
                        
                    // On n'est pas dans un cas d'erreur.
                    else {
                        // Rien dans le tableau de retour
                        // => Traitement
                        //$retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
                        //$retour['ch_num'] = "Go !!!";
                    }
                }
            }
        }
    }
    
    // Clic qqch d'inconnu !
    else {
        $retour['message_erreur'] = _T('simplecal:auteur_msg_erreur');
        $retour['ch_num'] = "What's that !!?";
    }
    
    return $retour;
}


function formulaires_choisir_auteurs_simplecal_traiter_dist($id_evenement){
       
    $id_auteur = trim(_request('ch_num'));
    
    // Traitement ajout
    if (_request('ajouter')) {
        $data = array();
        $data['id_evenement'] = $id_evenement;
        $data['id_auteur'] = $id_auteur;
        
        sql_insertq('spip_auteurs_evenements', $data);
        $retour = array();
        $retour['message_ok'] = _T('simplecal:auteur_msg_ajout');
    }
    
    // Traitement retrait
    else if (_request('retirer')) {
        sql_delete("spip_auteurs_evenements", "id_auteur=".sql_quote($id_auteur)." AND id_evenement=".sql_quote($id_evenement));
        $retour = array();
        $retour['message_ok'] = _T('simplecal:auteur_msg_retrait');
    }
    
    // Traitement autre ?
    else {
        $retour = array();
        $retour['message_ok'] = 'Aucun traitement...';    
    }
	
    return $retour;
}



?>
