<?php

function formulaires_reserv_charger_dist($idressource,$date_deb,$date_f,$idresa=false){
    include_spip('inc/config');

    list($dated,$heured)            = explode(' ',$date_deb);
    list($anneed,$moisd,$jourd)     = explode('-',$dated);
    list($heured,$minuted,$econded) = explode(':',$heured);
    $date_deb = date("d/m/Y H:i:s", mktime($heured, $minuted, $seconded, $moisd, $jourd, $anneed));

    list($datef,$heuref)            = explode(' ',$date_f);
    list($anneef,$moisf,$jourf)     = explode('-',$datef);
    list($heuref,$minutef,$econdef) = explode(':',$heuref);
    $date_f = date("d/m/Y H:i:s", mktime($heuref, $minutef, $secondef, $moisf, $jourf, $anneef));
    
    // recup des valeurs si resa existante
	if ($idresa)
		$vals_resa = sql_fetsel('*', 'spip_orr_reservations', 'id_orr_reservation='.intval($idresa));
	
    $valeurs = array(
        "nom_reservation" => ($idresa ? $vals_resa['orr_reservation_nom'] : ""),
        "id_ressource"    => intval($idressource),
        "date_debut"      => $date_deb,
        "date_fin"        => $date_f,
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
    
    //format de date correct
    if (!isset($erreurs['date_debut'])){
        list ($dated,$tempsd) = explode(' ',$date_debut);
        list ($jourd,$moisd,$anneed) = explode('/',$dated);
        if (!intval($jourd)or!intval($moisd)or!intval($anneed)or!preg_match("#^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$#", $tempsd)) {
            $erreurs['date_debut'] = _T('orr:erreur_reservation_format_date');
        }
    }
    if (!isset($erreurs['date_fin'])){
        list ($datef,$tempsf) = explode(' ',$date_fin);
        list ($jourf,$moisf,$anneef) = explode('/',$datef);
        if (!intval($jourf)or!intval($moisf)or!intval($anneef)or!preg_match("#^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$#", $tempsf)) {
            $erreurs['date_fin'] = _T('orr:erreur_reservation_format_date');
        }
    }

    // date de fin anterieur à la date de debut
    list($heured,$minuted,$seconded) = explode(':',$tempsd);
    list($heuref,$minutef,$secondef) = explode(':',$tempsf);
    $timestampd = mktime($heured,$minuted,$seconded,$moisd,$jourd,$anneed);
    $timestampf = mktime($heuref,$minutef,$secondef,$moisf,$jourf,$anneef);
    if ($timestampd>=$timestampf){
        $erreurs['date_fin'] =_T('orr:erreur_reservation_date_fin_debut');
    }

    // les dates choisies sont libres
//    include_spip('inc/compare_date');
    $date_debut = date("Y-m-d H:i:s", mktime ($heured,$minuted,0, $moisd, $jourd, $anneed));
    $date_fin   = date("Y-m-d H:i:s", mktime ($heuref,$minutef,0, $moisf, $jourf, $anneef));
    $resultat = orr_compare_date($date_debut,$date_fin,$idressource,$idresa);
	if ($resultat == "1"){
		$erreurs['date_debut'] =_T('orr:erreur_reservation_date_occupe');
		$erreurs['date_fin'] =_T('orr:erreur_reservation_date_occupe');
	}
    return $erreurs;
}


function formulaires_reserv_traiter_dist($idressource,$date_deb,$date_f,$idresa){
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
	$jourj = date("Y-m-d", mktime ($heured,$minuted,0, $moisd, $jourd, $anneed));
	$date_fin   = date("Y-m-d H:i:s", mktime ($heuref,$minutef,0, $moisf, $jourf, $anneef));
	
	$retour=array();
	$retour['message_ok'] = _T("orr:reservation_enregistree");
	$retour['redirect'] = "spip.php?page=orr&jourj=$jourj";

	// utilisation API editer_objet pour l'insertion en BDD
	// champs standards
	$objet = "orr_reservation";
	include_spip('action/editer_objet');
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
	if ($idresa)
		$id_objet = $idresa;
	else
		$id_objet = objet_inserer($objet);
	objet_modifier($objet, $id_objet, $set);

	// utilisation de l'API editer_liens pour la gestion de la table de lien entre
	// une reservation et une ressource
	include_spip('action/editer_liens');
	objet_associer(array("orr_reservation"=>$id_objet), array("orr_ressource"=>$idressource));

	return $retour;
}

?>
