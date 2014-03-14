<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function recherche_autorisation($idressource,$statut_connecte,$autorisation,$id_auteur,$id_resa=''){
    // zou! si on est webmestre, pas besoin d'aller plus loin: on est autorisé pour *tout*
    if (isset($GLOBALS['visiteur_session']['webmestre']) And $GLOBALS['visiteur_session']['webmestre'] == 'oui')
        return true;

    // Pour Modifier ou Supprimer une résa, on test si l'auteur connecté est 
    // le propriétaire de la résa
    $test_auteur = false;
    if ($id_resa) {
        $all = sql_allfetsel(
            "lien.id_objet" , "spip_orr_reservations_liens AS lien",
            array(
                "lien.objet = 'auteur'",
                "lien.id_orr_reservation =". intval($id_resa))
        ); 
        if ($all[0]['id_objet'] == $id_auteur) {
            $test_auteur = true;
        }
    }

    $result = false;
    $res = sql_select(
        array(
            "auto.id_orr_autorisation AS idauto",
            "auto.orr_type_objet AS type",
            "auto.orr_statut AS statut",
            "auto.id_auteur AS id_auteur",
            "auto.orr_autorisation_valeur AS valeur"),
        array(
            "spip_orr_autorisations AS auto",
            "spip_orr_autorisations_liens AS lien"),
        array(
            "auto.id_orr_autorisation = lien.id_orr_autorisation",
            "lien.objet='orr_ressource'",
            "lien.id_objet=$idressource")
    );

    while ($r = sql_fetch($res)) {

        // Si Modif ou suppression uniquement de ses résas
        // on prépare le test de droits 
        $valeur_statut = array("tous"=>"1","6forum"=>"2","1comite"=>"3","0minirezo"=>"4");
        $r_test = true;
        if (($autorisation == "S" OR $autorisation == "M"  ) AND 
            ((strpos($r['valeur'],"*") !== false) AND 
            ($valeur_statut[$r['statut']] >= $valeur_statut[$statut_connecte]) AND 
            $id_resa AND !$test_auteur)) 
                $r_test = false;

    //  Autorisation par statut
        if (($r['type'] == "statut") AND 
            ($valeur_statut[$r['statut']] <= $valeur_statut[$statut_connecte]) AND 
            (strpos($r['valeur'], $autorisation) !== false) AND
            $r_test) 
                $result = true;

    //   autorisation par grappe
        if ($r['type'] == "grappe") {
            $res_grappe = sql_select(
                array(
                    "lien.id_objet AS idgrappe_auteur"),
                array(
                    "spip_grappes AS grappe",
                    "spip_grappes_liens AS lien"),
                array(
                    "grappe.id_grappe = lien.id_grappe",
                    "lien.objet = 'auteur'"));

            while($rg = sql_fetch($res_grappe)){
                if (($rg['idgrappe_auteur'] == $id_auteur) AND (strpos($r['valeur'], $autorisation) !== false)) $result = true;
            }
        }
    //  autorisation par auteur
        if (($r['type'] == "auteur") AND ($r['id_auteur'] == $id_auteur) AND (strpos($r['valeur'], $autorisation) !== false)) $result = true;
    }
    return $result;
}

// declaration vide pour ce pipeline.
function orr_autoriser(){}


// -----------------
// Objet orr_ressources


// bouton de menu
function autoriser_orrressources_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
}


// creer
function autoriser_orrressource_creer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// voir les fiches completes
function autoriser_orrressource_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// modifier
function autoriser_orrressource_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// supprimer
function autoriser_orrressource_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}


// -----------------
// Objet orr_reservations

// creer
function autoriser_orrreservation_creer_dist($faire, $type, $id, $qui, $opt) {
    if ($qui['statut']) {
        $statut=$qui['statut'];
    }else {
        $statut="tous";
    }
    $id_auteur=$qui['id_auteur'];
	$autorisation="C";
	$resultat=recherche_autorisation($id,$statut,$autorisation,$id_auteur);
	return $resultat;
}

// voir les fiches completes
function autoriser_orrreservation_voir_dist($faire, $type, $id, $qui, $opt) {
    if ($qui['statut']) {
        $statut=$qui['statut'];
    }else {
        $statut="tous";
    }
    $id_auteur=$qui['id_auteur'];
	$autorisation="V";
	$resultat=recherche_autorisation($id,$statut,$autorisation,$id_auteur);
	return $resultat;
}

// modifier
function autoriser_orrreservation_modifier_dist($faire, $type, $id, $qui, $id_resa='') {
    if ($qui['statut']) {
        $statut=$qui['statut'];
    }else {
        $statut="tous";
    }
    $id_auteur=$qui['id_auteur'];
	$autorisation="M";
	$resultat=recherche_autorisation($id,$statut,$autorisation,$id_auteur,$id_resa);
	return $resultat;
}

// supprimer 
function autoriser_orrreservation_supprimer_dist($faire, $type, $id, $qui, $id_resa='') {
    if ($qui['statut']) {
        $statut=$qui['statut'];
    }else {
        $statut="tous";
    }
    $id_auteur=$qui['id_auteur'];
	$autorisation="S";
	$resultat=recherche_autorisation($id,$statut,$autorisation,$id_auteur,$id_resa);
	return $resultat;
}


// associer (lier / delier)
function autoriser_associerorrreservations_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

?>
