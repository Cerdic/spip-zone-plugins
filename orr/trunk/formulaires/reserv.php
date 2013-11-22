<?php

function formulaires_reserv_charger_dist($idressource,$date_deb,$date_f,$idresa=false){
    // test si l'utilisateur à le droit de creer une résa pour la ressource active
    if ($idressource AND !autoriser('creer','orr_reservation',intval($idressource)))
        return exit;

    include_spip('inc/config');
    list($dated,$heured)            = explode(' ',$date_deb);
    list($anneed,$moisd,$jourd)     = explode('-',$dated);
    list($heured,$minuted,$econded) = explode(':',$heured);
    $date_deb = date("d/m/Y H:i:s", mktime($heured, $minuted, $seconded, $moisd, $jourd, $anneed));

    list($datef,$heuref)            = explode(' ',$date_f);
    list($anneef,$moisf,$jourf)     = explode('-',$datef);
    list($heuref,$minutef,$econdef) = explode(':',$heuref);
    $date_f = date("d/m/Y H:i:s", mktime($heuref, $minutef, $secondef, $moisf, $jourf, $anneef));


    //Récup des noms de ressource sauf la ressource active    
    $result= sql_allfetsel('id_orr_ressource,orr_ressource_nom','spip_orr_ressources','id_orr_ressource !='.intval($idressource));

    foreach ($result as $Tressource) {
        // Test si l'utilisateur à le droit de creer une résa pour cette ressource
        if (autoriser('creer','orr_reservation',intval($Tressource['id_orr_ressource']))) 
            $Tressources[$Tressource['id_orr_ressource']] = $Tressource['orr_ressource_nom'];
    }
    // recup des valeurs si resa existante
	if ($idresa)
		$vals_resa = sql_fetsel('*', 'spip_orr_reservations', 'id_orr_reservation='.intval($idresa));
    $valeurs = array(
        "idreservation"          => intval($idresa),
        "choix_ressource_active" => "on",
        "liste_ressources"       => "",
        "nom_reservation"        => ($idresa ? $vals_resa['orr_reservation_nom'] : ""),
        "id_ressource"           => intval($idressource),
        "date_debut"             => $date_deb,
        "date_fin"               => $date_f,
        "liste_ressources"       => $Tressources, 
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

function formulaires_reserv_verifier_dist($idressource,$date_deb,$date_f,$idresa){
    $date_debut      = _request('date_debut');
    $date_fin        = _request('date_fin');
    $erreurs = array();
    //champs obligatoire
    foreach (array ('nom_reservation','date_debut','date_fin') as $obligatoire) {
        if (!_request($obligatoire)) 
            $erreurs[$obligatoire] = _T("info_obligatoire");
    }
    // Il faut au moins une ressource !!
    if (!_request('liste_ressources') AND !_request('choix_ressource_active')) {
            $erreurs["choix_ressource_active"] = _T("orr:ressource_obligatoire");
    }
   
    //format de date correct
    if (!isset($erreurs['date_debut'])){
        list ($dated,$tempsd)        = explode(' ',$date_debut);
        list ($jourd,$moisd,$anneed) = explode('/',$dated);
        if (!intval($jourd)or!intval($moisd)or!intval($anneed)or!preg_match("#^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$#", $tempsd)) {
            $erreurs['date_debut'] = _T('orr:erreur_reservation_format_date');
        }
    }
    if (!isset($erreurs['date_fin'])){
        list ($datef,$tempsf)        = explode(' ',$date_fin);
        list ($jourf,$moisf,$anneef) = explode('/',$datef);
        if (!intval($jourf)or!intval($moisf)or!intval($anneef)or!preg_match("#^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$#", $tempsf)) {
            $erreurs['date_fin'] = _T('orr:erreur_reservation_format_date');
        }
    }

    // date de fin anterieur à la date de debut
    list($heured,$minuted,$seconded) = explode(':',$tempsd);
    list($heuref,$minutef,$secondef) = explode(':',$tempsf);
    $timestampd = mktime(intval($heured),$minuted,$seconded,$moisd,$jourd,$anneed);
    $timestampf = mktime(intval($heuref),$minutef,$secondef,$moisf,$jourf,$anneef);
    if ($timestampd>=$timestampf){
        $erreurs['date_fin'] =_T('orr:erreur_reservation_date_fin_debut');
    }

    // les dates choisies sont libres
    $liste_ressources = array();
    // Si C'est une mise à jour, on ne traite que de la ressource sélectionnée)
    if ($idresa)
        $liste_ressources[] = $idressource;
    // fabrique un array : liste_ressources de toutes les ressources
    elseif (_request('choix_ressource_active')){
        $liste_ressources   = _request('liste_ressources');
        $liste_ressources[] = $idressource;
    }
    $date_debut = date("Y-m-d H:i:s", mktime (intval($heured),$minuted,0, $moisd, $jourd, $anneed));
    $date_fin   = date("Y-m-d H:i:s", mktime (intval($heuref),$minutef,0, $moisf, $jourf, $anneef));
    
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


function formulaires_reserv_traiter_dist($idressource,$date_deb,$date_f,$idresa){
    $liste_ressources = array();
	$nom_reservation        = _request('nom_reservation');
	$date_debut             = _request('date_debut');
	$date_fin               = _request('date_fin');
    $choix_ressource_active = _request('choix_ressource_active');

    // si C'est une mise à jour, on ne traite que de la ressource sélectionné)
    if ($idresa)
        $liste_ressources[] = $idressource;
    // fabrique un array : liste_ressources de toutes les ressources
    else {
        $liste_ressources   = _request('liste_ressources');
        $liste_ressources[] = $idressource;
    }

	list($jour_debut, $heure_debut) = explode(' ',$date_debut);
	list($jour_fin, $heure_fin)     = explode(' ',$date_fin);
	list($jourd,$moisd,$anneed)     = explode('/',$jour_debut);
	list($jourf,$moisf,$anneef)     = explode('/',$jour_fin);

	list($heured,$minuted) = explode(':',$heure_debut);
	list($heuref,$minutef) = explode(':',$heure_fin);

	$date_debut = date("Y-m-d H:i:s", mktime ($heured,$minuted,0, $moisd, $jourd, $anneed));
	$jourj      = date("Y-m-d", mktime ($heured,$minuted,0, $moisd, $jourd, $anneed));
	$date_fin   = date("Y-m-d H:i:s", mktime ($heuref,$minutef,0, $moisf, $jourf, $anneef));
	
	$retour=array();
	$retour['message_ok'] = _T("orr:reservation_enregistree");
	$retour['redirect']   = "spip.php?page=orr&jourj=$jourj";

	// utilisation API editer_objet pour l'insertion en BDD
	// champs standards
	$objet = "orr_reservation";
	include_spip('action/editer_objet');
    $set = array();

    // enregistrement pour chaque ressource
    foreach ($liste_ressources as $idressource) {
        if (!autoriser('creer','orr_reservation',intval($idressource)))
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
	    objet_associer(array("orr_reservation"=>$id_objet), array("orr_ressource"=>$idressource));
    }
	return $retour;
}

?>
