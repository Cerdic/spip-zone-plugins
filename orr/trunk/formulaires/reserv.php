<?php

function formulaires_reserv_charger_dist($idressource, $date_deb=false, $idresa=false, $jourj='', $vue='', $id_auteur){
    include_spip('inc/config');
    // mise à jour: récup les infos de la résa selectionnée
    if ($idresa) {
		$vals_resa = sql_fetsel('*', 'spip_orr_reservations', 'id_orr_reservation='.intval($idresa));
        $nom_resa  = $vals_resa['orr_reservation_nom'];
        $date_debut = date("d/m/Y H:i:s", strtotime($vals_resa['orr_date_debut']));
        $date_fin = date("d/m/Y H:i:s", strtotime($vals_resa['orr_date_fin']));
    }
    else {
        $nom_resa = "";
        // recup heures par défaut de debut / fin dans les options de config
        if ($heure_debut = lire_config('orr/heure_debut'))
			$date_debut = date("d/m/Y H:i:s", ($date_deb ? strtotime(str_replace('00:00:00', $heure_debut, $date_deb)) : date().' '.$heure_debut));
		else
			$date_debut = date("d/m/Y H:i:s", ($date_deb ? strtotime($date_deb) : date().' 00:00:00'));
        if ($heure_fin = lire_config('orr/heure_fin'))
			$date_fin = date("d/m/Y H:i:s", ($date_deb ? strtotime(str_replace('00:00:00', $heure_fin, $date_deb)) : date().' '.$heure_fin));
		else
			$date_fin = date("d/m/Y H:i:s", ($date_deb ? strtotime($date_deb) : date().' 00:00:00'));
    }

    $valeurs = array(
        "nom_reservation"   => $nom_resa, 
        "id_ressource"      => $idressource,
        "date_debut"        => $date_debut,
        "date_fin"          => $date_fin,
        "id_reservation"    => $idresa,
    );
    // champs extra
    if (lire_config("champs_extras_spip_orr_reservations")) {
        // Récupération du nom des champs extra
        $Tchamps_extra = orr_nom_champs_extra("spip_orr_reservations");
        // initialisation de la valeurs des champs extra
        foreach ($Tchamps_extra as $key) 
            $valeurs[$key] = ($idresa ? $vals_resa[$key] : "");
    }

    return $valeurs;
}

function formulaires_reserv_verifier_dist($idressource, $date_deb, $idresa, $jourj, $vue, $id_auteur){
    include_spip('inc/autoriser');
    // on passe les dates du timepicker en format d-m-a h:m:s pour que strtotime() soit capable de les utiliser
    $date_debut = str_replace('/','-',_request('date_debut'));
    $date_fin   = str_replace('/','-',_request('date_fin'));
    $erreurs = array();
        
    //champs obligatoires
    foreach (array ('nom_reservation','date_debut','date_fin') as $obligatoire) {
        if (!_request($obligatoire)) 
            $erreurs[$obligatoire] = _T("info_obligatoire");
    }
    if (count($erreurs))
		return $erreurs;
   
    //format de date correct
    foreach (array('date_debut', 'date_fin') as $a_tester) {
		if (!intval(strtotime($$a_tester)))
			$erreurs[$a_tester] = _T('orr:erreur_reservation_format_date');
    }
    if (count($erreurs))
		return $erreurs;

    // date de fin anterieure à la date de debut
    if (strtotime($date_debut) >= strtotime($date_fin)) {
        $erreurs['date_fin'] =_T('orr:erreur_reservation_date_fin_debut');
        return $erreurs;
    }
    
	// faire l'array des ressources à traiter 
	$liste_ressources = array();
    $recup_ressources = _request('liste_ressources');
	if ($idresa)
		$liste_ressources[] = intval($idressource);
	elseif ($recup_ressources) {
		foreach ($recup_ressources as $ress)
			$liste_ressources[] = intval($ress);
	}
    // Il faut au moins une ressource !!
    if (!count($liste_ressources)) {
        $erreurs["liste_ressources"] = _T("orr:ressource_obligatoire");
        return $erreurs;
    }
    
	// tester si l'utilisateur a le droit de creer une résa pour les ressources sélectionnées
	foreach ($liste_ressources as $ress) {
		if (!autoriser('creer','orr_reservation', $ress))
		$erreurs['message_erreur'] = _T("orr:creation_autorisation_interdite");
	}
	if (count($erreurs))
		return $erreurs;

	$date_debut = date("Y-m-d H:i:s", strtotime($date_debut));
	$date_fin   = date("Y-m-d H:i:s", strtotime($date_fin));

	$resultat = array();
	foreach ($liste_ressources as $idressource) {
		if (orr_compare_date($date_debut,$date_fin,$idressource,$idresa))
			$resultat[] = $idressource;
	}
	if ($resultat){
		$nom_ressources = array();
		$nom_ressources = sql_allfetsel('orr_ressource_nom', 'spip_orr_ressources', sql_in('id_orr_ressource', $resultat));
		foreach ($nom_ressources as $ressource) {
			$Tressources[] = $ressource['orr_ressource_nom'];
		}
		$pluriel = count($Tressources)>1 ? "les ressources" : "la ressource";
		$affichage_ressource = implode(", ",$Tressources);
		$erreurs['date_debut'] = _T('orr:erreur_reservation_date_occupe',array('ressource' => $affichage_ressource,'pluriel' => $pluriel));
		$erreurs['date_fin']   = _T('orr:erreur_reservation_date_occupe',array('ressource' => $affichage_ressource,'pluriel' => $pluriel));
	}

    return $erreurs;
}


function formulaires_reserv_traiter_dist($idressource, $date_deb, $idresa, $jourj, $vue, $id_auteur){
    $liste_ressources = array();
	$nom_reservation  = _request('nom_reservation');
	$date_debut       = str_replace('/','-',_request('date_debut'));
	$date_fin         = str_replace('/','-',_request('date_fin'));

    // fabriquer liste_ressources: array de toutes les ressources à traiter
    // si c'est une mise à jour, on ne traite que de la ressource sélectionnée
    if ($idresa)
        $liste_ressources[] = $idressource;
    else {
        $liste_ressources = _request('liste_ressources');
    }

	$date_debut = date("Y-m-d H:i:s", strtotime($date_debut));
//	$jourj      = date("Y-m-d", strtotime($date_debut));
	$date_fin   = date("Y-m-d H:i:s", strtotime($date_fin));
	
	$retour=array();
	$retour['message_ok'] = _T("orr:reservation_enregistree");
	$retour['redirect']   = "spip.php?page=orr&jourj=$jourj&vue=$vue";

	// utilisation API editer_objet pour l'insertion en BDD
	// champs standards
	$objet = "orr_reservation";
	include_spip('action/editer_objet');
    $set = array();

    // enregistrement pour chaque ressource
    foreach ($liste_ressources as $idressource) {
        if (!autoriser('creer','orr_reservation', intval($idressource)))
            continue;
	    $set = array (
		    'id_orr_ressource'    => $idressource,
		    'orr_reservation_nom' => $nom_reservation,
		    'orr_date_debut'      => $date_debut,
		    'orr_date_fin'        => $date_fin
	    );
	    // champs extras
	    $nom_champs_extra = orr_nom_champs_extra("spip_orr_reservations");
	    foreach ($nom_champs_extra as $key) 
		    $set[$key] = _request($key);

	    // enregistrement
	    $id_objet=$idresa ? $idresa : objet_inserer($objet); 
	    objet_modifier($objet, $id_objet, $set);

	    // utilisation de l'API editer_liens pour la gestion de la table de lien entre
	    // une reservation et une ressource
	    include_spip('action/editer_liens');
	    objet_associer(array("orr_reservation" => $id_objet), array("orr_ressource" => $idressource,"auteur" => $id_auteur));
    }
	return $retour;
}

?>
