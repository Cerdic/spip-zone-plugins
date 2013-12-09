<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_orr_autorisation_charger_dist($id_autorisation="",$redirect="",$associer_objet=""){
	if ($id_autorisation != '' AND $id_autorisation != 'oui')
		$row = sql_fetsel("*", "spip_orr_autorisations", "id_orr_autorisation=$id_autorisation");
	$Tdroits = explode('-', $row["orr_autorisation_valeur"]);
    $valeurs = array(
        "orr_type"  	=> $row["orr_type_objet"],
        "orr_droit"		=> $Tdroits,
// à priori ces 3 là ne servent pas à grand chose ici: les selects ne sont pas présents lors du chargement du form   
        "orr_statut"	=> $row["orr_statut"] != '' ? $row["orr_statut"] : "",
        "orr_grappe"	=> $row["id_grappe"] ? $row["id_grappe"] : "",
        "orr_auteur"	=> $row["id_auteur"] ? $row["id_auteur"] : "",
// stockage des valeurs enregistrées si edition d'une autorisation existante pour la transmission ajax        
        "val_statut"	=> $row["orr_statut"] != '' ? $row["orr_statut"] : "",
        "val_auteur"	=> $row["id_auteur"] ? $row["id_auteur"] : "",
        "val_grappe"	=> $row["id_grappe"] ? $row["id_grappe"] : "",
    );
return $valeurs;
}
    
function formulaires_editer_orr_autorisation_verifier_dist($id_autorisation="",$redirect="",$associer_objet=""){
    $orr_statut	= _request('orr_statut');
    $orr_grappe	= _request('orr_grappe');
    $orr_auteur	= _request('orr_auteur');
    $orr_droit	= _request('orr_droit');
    $orr_type	= _request('orr_type');
        
    $erreurs = array();
    // Il faut un type et un seul
    $controle_type = ($orr_statut != ''? 1 : 0) + ($orr_grappe!= ''? 1 : 0) + ($orr_auteur!= ''? 1 : 0);
	if ($controle_type > 1) 
		$erreurs['message_erreur']	= "Il faut choisir 1 seul type d'utilisateur";
	if ($controle_type < 1) 
		$erreurs['message_erreur']	= "Il faut choisir au moins 1 auteur ou 1 statut ou 1 grappe";

    //Il y a des erreurs
    if (count($erreurs)) 
        $erreurs['message_erreur'] = 'Votre saisie contient des erreurs !<br>$controle_type: '.$controle_type.'<br><strong>'.$erreurs['message_erreur'].'</strong>';

    return $erreurs;
}

function formulaires_editer_orr_autorisation_traiter_dist($id_autorisation="",$redirect="",$associer_objet=""){
    $orr_statut	= _request('orr_statut');
    $orr_grappe	= _request('orr_grappe');
    $orr_auteur	= _request('orr_auteur');
    $orr_droit	= _request('orr_droit');
    $orr_type	= _request('orr_type');


    // Détermination du type d'objet et du nom à afficher : orr_autorisation_nom
	$valeur_statuts=array("tous" => "Tous","6forum" => "Visiteur","1comite"=>"Rédacteur","0minirezo"=>"Administrateur");

    if ($orr_type == "statut"){
        $type_objet = "statut";
        $autorisation_nom = $valeur_statuts[$orr_statut];
    }
    if ($orr_type == "grappe"){
        $type_objet = "grappe";
        $autorisation_nom = "Grappe n°$orr_grappe";
    }
    if ($orr_type == "auteur") {
        $type_objet = "auteur";
        $autorisation_nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . intval($orr_auteur));
    }

/* obsolete: on stocke les droits sous la forme V-M-C-S désormais
    //Détermination de la somme des droits
    $somme_droit = 0;
	foreach ($orr_droit as $key) {
		$somme_droit += $key;
	}
	// Détermination de la valeur à afficher : orr_autorisation_valeur
	$valeur_droits = array("2"=>"V","4"=>"M","6"=>"V-M","8"=>"C","10"=>"V-C","12"=>"M-C","14"=>"V-M-C","16"=>"S","18"=>"V-S","20"=>"M-S","24"=>"C-S","26"=>"V-C-S","28"=>"M-C-S","30"=>"V-M-C-S");
	$autorisation_valeur = $valeur_droits[$somme_droit];
*/
	$autorisation_valeur = join('-', $orr_droit);

	list($nom_objet,$id_ressource) = explode("|",$associer_objet); 


       //insertion en bdd
	include_spip('action/editer_objet');
	$objet = "orr_autorisation";
	if (intval($id_autorisation) == '') 
		$id_objet = objet_inserer($objet);
	else
		$id_objet = $id_autorisation;
	$set = array (
		'orr_type_objet'          => "$type_objet",
		'id_grappe'               => "$orr_grappe",
		'orr_statut'              => "$orr_statut",
		'id_auteur'               => "$orr_auteur",
		'orr_droit'               => "$somme_droit",
		'orr_autorisation_nom'    => "$autorisation_nom",
		'orr_autorisation_valeur' => "$autorisation_valeur"
	);
	objet_modifier($objet, $id_objet, $set);

	// Lien entre l'autorisation et la ressource dans la table orr_autorisations_liens
	include_spip('action/editer_liens');
	objet_associer(array("orr_autorisation" => $id_objet), array("orr_ressource" => $id_ressource));

	$retour = array();
	$retour['message_ok'] = "bravo:$autorisation_valeur ";
	$retour['redirect'] = "$redirect";
    //$retour['editable'] = true;

    return $retour;
}
?>


