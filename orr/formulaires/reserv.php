<?php

function formulaires_reserv_charger_dist($id,$date_deb){
    list($annee,$mois,$jour)=explode('-',$date_deb);
    $date_deb = date("d/m/Y H:i:s", mktime(0, 0, 0, $mois, $jour, $annee));
    $valeurs = array(
        "nom_ressource"   => "",
        "nom_reservation" => "",
        "id_ressource"    => $id,
        "date_debut"      => $date_deb,
        "date_fin"        => $date_deb,
    );
    return $valeurs;
}

function formulaires_reserv_verifier_dist($id,$date_deb){
    $date_debut      = _request('date_debut');
    $date_fin        = _request('date_fin');
    $erreurs = array();
    //champs obligatoire
    foreach (array ('nom_reservation','date_debut','date_fin') as $obligatoire) {
        if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = 'Ce champs est obligatoire';
        }
    }
    //format de date correct
    if (!isset($erreurs['date_debut'])){
        list ($dated,$tempsd) = explode(' ',$date_debut);
        list ($jourd,$moisd,$anneed) = explode('/',$dated);
        if (!intval($jourd)or!intval($moisd)or!intval($anneed)or!preg_match("#^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$#", $tempsd)) {
            $erreurs['date_debut'] = "Ce format de data n'est pas reconnu.";
        }
    }
    if (!isset($erreurs['date_fin'])){
        list ($datef,$tempsf) = explode(' ',$date_fin);
        list ($jourf,$moisf,$anneef) = explode('/',$datef);
        if (!intval($jourf)or!intval($moisf)or!intval($anneef)or!preg_match("#^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$#", $tempsf)) {
            $erreurs['date_fin'] = "Ce format de data n'est pas reconnu.";
        }
    }

    // date de fin anterieur à la date de debut
    list($heured,$minuted,$seconded) = explode(':',$tempsd);
    list($heuref,$minutef,$secondef) = explode(':',$tempsf);
    $timestampd = mktime($heured,$minuted,$seconded,$jourd,$moisd,$anneed);
    $timestampf = mktime($heuref,$minutef,$secondef,$jourf,$moisf,$anneef);
    if ($timestampd>$timestampf){
        $erreurs['date_fin'] = "date de fin antérieur à la date de début";
    }

    // les dates choisies sont libres
    include_spip('inc/compare_date');
    $date_debut = date("Y-m-d H:i:s", mktime ($heured,$minuted,0, $moisd, $jourd, $anneed));
    $date_fin   = date("Y-m-d H:i:s", mktime ($heuref,$minutef,0, $moisf, $jourf, $anneef));
    $retour_debut=compare_date($date_debut,$id);
    $retour_fin=compare_date($date_fin,$id);

    if ($retour_debut=="2") {
        $erreurs['date_debut'] = "Il y a déja une réservation à ce moment là !";
    }
    if ($retour_fin=="2") {
        $erreurs['date_fin'] = "Il y a déja une réservation à ce moment là !";
    }
    if ($retour_debut=="1" and $retour_fin=="3") {
        $erreurs['date_fin'] = "Il y a déja une réservation à ce moment là !";
        $erreurs['date_debut'] = "Il y a déja une réservation à ce moment là !";
    }
    if (count($erreurs)) {
        $erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
    }
    return $erreurs;
}


function formulaires_reserv_traiter_dist($id,$date_deb){
    $nom_reservation = _request('nom_reservation');
    $date_debut      = _request('date_debut');
    $date_fin        = _request('date_fin');

    list($jour_debut, $heure_debut) = explode(' ',$date_debut);
    list($jour_fin, $heure_fin) = explode(' ',$date_fin);
    list($jourd,$moisd,$anneed) = explode('/',$jour_debut);
    list($jourf,$moisf,$anneef) = explode('/',$jour_fin);

    list($heured,$minuted) = explode(':',$heure_debut);
    list($heuref,$minutef) = explode(':',$heure_fin);

    $date_debut = date("Y-m-d H:i:s", mktime ($heured,$minuted,0, $moisd, $jourd, $anneed));
    $date_fin   = date("Y-m-d H:i:s", mktime ($heuref,$minutef,0, $moisf, $jourf, $anneef));

    $retour['message_ok'] = "bravo";
    $retour['redirect'] = "spip.php?page=affichage_orr";

    $objet = "orr_reservation";
    // utilisation API editer_objet pour l'insertion en BDD'
    include_spip('action/editer_objet');
    $id_objet = objet_inserer($objet);
    $set = array (
        'id_orr_ressource'    => $id,
        'orr_reservation_nom' => $nom_reservation,
        'orr_date_debut'      => $date_debut,
        'orr_date_fin'        => $date_fin
    );
    objet_modifier($objet, $id_objet, $set);
    // utilisation de l'API editer_liens pour la gestion de la table de lien entre
    // une reservation et une ressource
    include_spip('action/editer_liens');
    objet_associer(array("orr_reservation"=>$id_objet), array("orr_ressource"=>$id));

    return $retour;
}
?>
